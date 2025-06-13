<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\AppointmentLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Service;

class AppointmentRepository extends BaseRepository
{
    function getModel()
    {
        return Appointment::class;
    }

    public function getAppointments($perPage, $keyword = null, $status = null)
    {
        $query = Appointment::with(['patient', 'logs.employee', 'appointmentServices.service']);

        if (!empty($keyword)) {
            $query->whereHas('patient', function ($q) use ($keyword) {
                $q->where('fullname', 'LIKE', "%$keyword%");
            });
        }

        if (!empty($status) && $status !== "all") {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    public function updateAppointment($id, $data, $employee)
    {
        DB::beginTransaction();

        try {
            $appointment = Appointment::with(['patient', 'appointmentServices'])->find($id);
            if (!$appointment) {
                return ['success' => false, 'message' => 'Appointment not found'];
            }
            $oldStatus = $appointment->status;

            // Update data
            $appointment->update([
                'bookingDate' => $data['bookingDate'] ?? $appointment->bookingDate,
                'appointmentTime' => $data['appointmentTime'] ?? $appointment->appointmentTime,
                'status' => $data['status'] ?? $appointment->status,
                'message' => $data['message'] ?? $appointment->message,
                'estimated_duration' => $data['totalEstimatedDuration'] ?? $appointment->estimated_duration,
                'notes' => $data['notes'] ?? $appointment->notes,
                'symptoms' => $data['symptoms'] ?? $appointment->symptoms,
                'appointment_type' => $data['appointment_type'] ?? $appointment->appointment_type,
            ]);

            // Update patient information
            if ($appointment->patient) {
                $patientData = [
                    'fullname' => $data['fullname'] ?? $appointment->patient->fullname,
                    'phoneNumber' => $data['phoneNumber'] ?? $appointment->patient->phoneNumber,
                    'birthdate' => $data['birthdate'] ?? $appointment->patient->birthdate,
                    'email' => $data['email'] ?? $appointment->patient->email,
                ];

                $hasChanges = false;
                foreach ($patientData as $key => $value) {
                    if ($value !== $appointment->patient->$key) {
                        $hasChanges = true;
                        break;
                    }
                }
                if ($hasChanges) {
                    $appointment->patient->update($patientData);
                }
            }

            // Update services if provided
            if (isset($data['services']) && is_array($data['services'])) {
                // Delete existing services
                $appointment->appointmentServices()->delete();
                
                // Add new services
                foreach ($data['services'] as $service) {
                    $appointment->appointmentServices()->create([
                        'service_id' => $service['id'],
                        'notes' => $service['notes'] ?? null
                    ]);
                }
            }

            // Log changes
            if (isset($data['status']) && $data['status'] !== $oldStatus) {
                AppointmentLog::create([
                    'appointment_id' => $appointment->id,
                    'status' => $data['status'],
                    'employee_id' => $employee->id,
                    'created_at' => now()
                ]);
            }

            DB::commit();
            return [
                'success' => true, 
                'message' => 'Update successful',
                'data' => $appointment->load(['patient', 'appointmentServices.service'])
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage()];
        }
    }

    /**
     * Đếm số lịch hẹn đã xác nhận trong khung giờ (theo estimated_duration thực tế của từng lịch)
     */
    public function getConfirmedAppointmentsInTimeRange($data)
    {
        $date = $data['date'];
        $startTime = $data['time'];
        $durationValue = (int) ($data['durationValue'] ?? 1);
        if (!$date || !$startTime) {
            return response()->json(['error' => 'Date and start time are required'], 400);
        }

        try {
            $bookingDate = Carbon::parse($date);
            $startHour = Carbon::parse("{$date} {$startTime}");
            $endHour = $startHour->copy()->addMinutes($durationValue * 60);

            // Đếm số lịch overlap chuẩn
            $count = Appointment::where('status', 'Confirmed')
                ->whereDate('bookingDate', $bookingDate->toDateString())
                ->where(function ($q) use ($startHour, $endHour) {
                    $q->whereRaw(
                        '(appointmentTime < ?) AND (DATE_ADD(appointmentTime, INTERVAL estimated_duration MINUTE) > ?)',
                        [$endHour->format('H:i:s'), $startHour->format('H:i:s')]
                    );
                })
                ->count();

            return response()->json([
                'time_range' => [
                    'start' => $startHour->format('H:i'),
                    'end' => $endHour->format('H:i'),
                ],
                'confirmed_count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date or time format: ' . $e->getMessage()], 400);
        }
    }
    public function checkAndSuggestSlotWithSubBlock($date, $startTime, $services, $maxSuggestions = 5)
    {
        $newDuration = array_sum(array_column($services, 'estimated_duration'));
        $appointmentStart = Carbon::parse("$date $startTime");
        $appointmentEnd = $appointmentStart->copy()->addMinutes($newDuration);

        // B1. Lấy số bác sĩ trực đủ cover toàn khoảng appointment mới
        $doctorCount = $this->getDoctorCountSlot($date, $startTime, $appointmentEnd->format('H:i'));

        if ($doctorCount === 0) {
            return [
                'available' => false,
                'error' => 'No doctors available on this date',
                'suggestions' => []
            ];
        }

        // B2. Sub-block 2 tiếng check
        $subBlockCheck = $this->checkSubBlock2Hours($date, $appointmentStart, $appointmentEnd, $newDuration, $doctorCount);
        if (!$subBlockCheck['success']) {
            // Gợi ý slot khác nếu slot này bị overload
            $suggestions = $this->suggestSlots2Hours($date, $services, $doctorCount, $maxSuggestions, $startTime);
            return [
                'available' => false,
                'reason' => 'Overload at sub-block: ' . $subBlockCheck['fail_subblock'],
                'suggestions' => $suggestions
            ];
        }

        // B3. Slot-level overlap check
        $overlapCount = Appointment::where('status', 'Confirmed')
            ->whereDate('bookingDate', $date)
            ->where(function ($q) use ($appointmentStart, $appointmentEnd) {
                $q->whereRaw(
                    '(appointmentTime < ?) AND (DATE_ADD(appointmentTime, INTERVAL estimated_duration MINUTE) > ?)',
                    [$appointmentEnd->format('H:i:s'), $appointmentStart->format('H:i:s')]
                );
            })
            ->count();

        if ($overlapCount >= $doctorCount) {
            $suggestions = $this->suggestSlots2Hours($date, $services, $doctorCount, $maxSuggestions, $startTime);
            return [
                'available' => false,
                'reason' => 'Too many concurrent appointments',
                'suggestions' => $suggestions
            ];
        }

        // Nếu mọi check đều pass
        return [
            'available' => true,
            'start_time' => $startTime,
            'end_time' => $appointmentEnd->format('H:i')
        ];
    }
    public function checkSubBlock2Hours($date, $appointmentStart, $appointmentEnd, $newDuration, $doctorCount)
    {
        $startHour = floor($appointmentStart->hour / 2) * 2; // Bắt đầu sub-block
        $endHour = ceil(($appointmentEnd->hour + ($appointmentEnd->minute > 0 ? 1 : 0)) / 2) * 2;  // Kết thúc sub-block
    
        for ($h = $startHour; $h < $endHour; $h += 2) {
            $subStart = $appointmentStart->copy()->setTime($h, 0, 0);
            $subEnd = $subStart->copy()->addHours(2);
    
            // Tính overlap của ca mới với sub-block
            $overlapStart = $appointmentStart->greaterThan($subStart) ? $appointmentStart : $subStart;
            $overlapEnd = $appointmentEnd->lessThan($subEnd) ? $appointmentEnd : $subEnd;
            $newOverlap = max(0, $overlapEnd->diffInMinutes($overlapStart, false));
    
            // Tính tổng workload đã có trong sub-block
            $existingAppointments = Appointment::where('status', 'Confirmed')
                ->whereDate('bookingDate', $date)
                ->where(function ($q) use ($subStart, $subEnd) {
                    $q->whereRaw(
                        '(appointmentTime < ?) AND (DATE_ADD(appointmentTime, INTERVAL estimated_duration MINUTE) > ?)',
                        [$subEnd->format('H:i:s'), $subStart->format('H:i:s')]
                    );
                })
                ->get(['appointmentTime', 'estimated_duration']);
    
            $existingOverlapSum = 0;
            foreach ($existingAppointments as $ex) {
                $exStart = Carbon::parse("$date {$ex->appointmentTime}");
                $exEnd = $exStart->copy()->addMinutes($ex->estimated_duration);
                $exOverlapStart = $exStart->greaterThan($subStart) ? $exStart : $subStart;
                $exOverlapEnd = $exEnd->lessThan($subEnd) ? $exEnd : $subEnd;
                $existOverlap = max(0, $exOverlapEnd->diffInMinutes($exOverlapStart, false));
                $existingOverlapSum += $existOverlap;
            }
    
            $capacity = $doctorCount * 120; // 2 giờ x số bác sĩ
            if ($existingOverlapSum + $newOverlap > $capacity) {
                return [
                    'success' => false,
                    'fail_subblock' => sprintf("%02d:00–%02d:00", $h, $h + 2)
                ];
            }
        }
        return ['success' => true];
    }
    public function suggestSlots2Hours($date, $services, $doctorCount, $maxSuggestions = 5, $excludeStartTime = null)
    {
        $totalDuration = array_sum(array_column($services, 'estimated_duration'));
        $suggestions = [];
        $slotLength = 15;
        $startTime = Carbon::parse("$date 07:00");
        $endTime = Carbon::parse("$date 21:00");

        while ($startTime->copy()->addMinutes($totalDuration) <= $endTime) {
            $slotEnd = $startTime->copy()->addMinutes($totalDuration);
            $currentSlotStart = $startTime->format('H:i');
            // Bỏ qua slot yêu cầu ban đầu (nếu có)
            if ($excludeStartTime && $currentSlotStart === $excludeStartTime) {
                $startTime->addMinutes($slotLength);
                continue;
            }
            // Kiểm tra sub-block
            $pass = $this->checkSubBlock2Hours($date, $startTime, $slotEnd, $totalDuration, $doctorCount)['success'];
            // Kiểm tra slot-level overlap
            $overlapCount = Appointment::where('status', 'Confirmed')
                ->whereDate('bookingDate', $date)
                ->where(function ($q) use ($startTime, $slotEnd) {
                    $q->whereRaw(
                        '(appointmentTime < ?) AND (DATE_ADD(appointmentTime, INTERVAL estimated_duration MINUTE) > ?)',
                        [$slotEnd->format('H:i:s'), $startTime->format('H:i:s')]
                    );
                })
                ->count();
            if ($pass && $overlapCount < $doctorCount) {
                $suggestions[] = [
                    'start_time' => $currentSlotStart,
                    'end_time' => $slotEnd->format('H:i')
                ];
                if (count($suggestions) >= $maxSuggestions) {
                    break;
                }
            }
            $startTime->addMinutes($slotLength);
        }
        return $suggestions;
    }

    /**
     * Đếm số bác sĩ trực đủ cover toàn khoảng appointment
     */
    public function getDoctorCountSlot($date, $startTime, $endTime)
    {
        return DB::table('work_schedules')
            ->join('work_schedule_details', 'work_schedules.id', '=', 'work_schedule_details.workScheduleId')
            ->whereDate('work_schedules.registerDate', $date)
            ->where('work_schedule_details.status', 'working')
            ->where('work_schedule_details.startTime', '<=', $startTime)
            ->where('work_schedule_details.endTime', '>=', $endTime)
            ->select('work_schedules.idEmployee')
            ->distinct()
            ->count();
    }
  

  
}