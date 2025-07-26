<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\WorkSchedule;
use App\Models\AppointmentLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use App\Models\Service;

class AppointmentRepository extends BaseRepository
{
    function getModel()
    {
        return Appointment::class;
    }

    public function getAppointments($perPage, $keyword = null, $status = null, $userRole = null, $date = null)
    {
        $query = Appointment::with(['patient', 'logs.employee', 'appointmentServices.service','lockedBy']);

        // Áp dụng bộ lọc theo từ khóa
        if (!empty($keyword)) {
            $query->whereHas('patient', function ($q) use ($keyword) {
                $q->where('fullname', 'LIKE', "%$keyword%");
            });
        }

        // Áp dụng bộ lọc theo trạng thái
        if (!empty($status) && $status !== "all") {
            $query->where('status', $status);
        }

        // Áp dụng bộ lọc theo ngày
        if (!empty($date)) {
            $query->whereDate('bookingDate', $date);
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
            if (isset($data['status'])) {
                // Cho phép ghi log nếu:
                // 1. Trạng thái mới khác trạng thái cũ
                // 2. Hoặc trạng thái mới là Called Failed
                if ($data['status'] !== $oldStatus || $data['status'] === 'Called Failed') {
                    AppointmentLog::create([
                        'appointment_id' => $appointment->id,
                        'status' => $data['status'],
                        'employee_id' => $employee->id,
                        'created_at' => now()
                    ]);
                }
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
     /**
     * Kiểm và gợi ý slot: slotStart ban đầu + estimatedDuration
     *
     * @param string $date              // "YYYY-MM-DD"
     * @param string $startTime         // "HH:mm"
     * @param int    $estimatedDuration // phút, chưa cộng buffer
     * @param int    $maxSuggestions    // tối đa số slot gợi ý (slot 15-phút) hoặc interval tùy FE xử lý
     * @return array
     *   - nếu available=true: ['available'=>true, 'start_time'=>..., 'end_time'=>...]
     *   - nếu available=false: ['available'=>false, 'reason'=>..., 'suggestions'=>[ ['start_time'=>'HH:mm','end_time'=>'HH:mm'], ... ], 
     *                             'intervals'=>[ ['start'=>'HH:mm','end'=>'HH:mm'], ... ] ]
     */
    public function checkAndSuggestSlotWithSubBlock(string $date, string $startTime, int $estimatedDuration, int $maxSuggestions = 15): array
    {

        // 1. Validate date/time
        try {
            $appointmentStart = Carbon::parse("$date $startTime");
        } catch (\Exception $e) {
            return [
                'available'=>false,
                'error'=>'Invalid date/time format',
                'suggestions'=>[],
                'intervals'=>[],
            ];
        }
        // Không cho phép đặt quá khứ
        if ($appointmentStart->lt(Carbon::now())) {
            return [
                'available'=>false,
                'reason'=>'Cannot book past time',
                'suggestions'=>[],
                'intervals'=>[],
            ];
        }
        // 2. Buffer và tính thời gian kết thúc
        $bufferMinutes = 10;
        $durationWithBuffer = $estimatedDuration + $bufferMinutes;
        $appointmentEnd = $appointmentStart->copy()->addMinutes($durationWithBuffer);

        // 3. Xác định shift chứa startTime
        $shiftRange = $this->getShiftRange($date, $startTime);
        if (!$shiftRange) {
            return [
                'available'=>false,
                'reason'=>'Start time is not within working hours',
                'suggestions'=>[],
                'intervals'=>[],
            ];
        }
        $shiftStart = $shiftRange['shiftStart'];
        $shiftEnd   = $shiftRange['shiftEnd'];

        // 4. Kiểm appointmentEnd trong ca
        if ($appointmentEnd->gt($shiftEnd)) {
            $latestStart = $shiftEnd->copy()->subMinutes($durationWithBuffer);
            $msg = 'Appointment exceeds shift time. You need to choose start time <= ' . $latestStart->format('H:i');
            return [
                'available'=>false,
                'reason'=>$msg,
                'suggestions'=>[],
                'intervals'=>[],
            ];
        }

        // 5. Lấy số bác sĩ tại thời điểm startTime
        $doctorCount = $this->getDoctorCountByShift($date, $startTime);
        if ($doctorCount <= 0) {
            return [
                'available'=>false,
                'reason'=>'No doctors available in this shift',
                'suggestions'=>[],
                'intervals'=>[],
            ];
        }

        // 6. Preload existing Confirmed appointments trong ngày
        $existingAll = Appointment::where('status','Confirmed')
            ->whereDate('bookingDate',$date)
            ->get(['appointmentTime','estimated_duration'])
            ->toArray();

        // 7. Kiểm workload+concurrency per sub-block trong shift hiện tại
        $subBlockMinutes = 60;
        $combinedCheck = $this->checkWorkloadAndConcurrencyInSubBlocks(
            $date,
            $appointmentStart,
            $appointmentEnd,
            $doctorCount,
            $bufferMinutes,
            $subBlockMinutes,
            $existingAll,
            $shiftStart,
            $shiftEnd
        );
        if (!$combinedCheck['success']) {
            $failSb = $combinedCheck['fail_subblock'] ?? '';
            // Gợi ý across shifts, bỏ doctorCount param vì sẽ lấy bên trong
            $suggestions = $this->suggestSlotsAcrossShifts(
                $date,
                $estimatedDuration,
                $maxSuggestions,
                $appointmentStart
            );
            // Group thành intervals nếu muốn
            $intervals = $this->groupContinuousSuggestions($suggestions);
            // Có thể sort intervals theo độ dài (tùy FE)
            usort($intervals, function($a, $b) {
                // compute length in minutes
                [$h1,$m1] = explode(':',$a['start']);
                [$hh1,$mm1] = explode(':',$a['end']);
                $len1 = intval($hh1)*60 + intval($mm1) - (intval($h1)*60 + intval($m1));
                [$h2,$m2] = explode(':',$b['start']);
                [$hh2,$mm2] = explode(':',$b['end']);
                $len2 = intval($hh2)*60 + intval($mm2) - (intval($h2)*60 + intval($m2));
                return $len2 <=> $len1; // giảm dần
            });
            return [
                'available'=>false,
                'reason'=>'Overload at sub-block: '.$failSb,
                'fail_subblock'=>$failSb,
                'suggestions'=>$suggestions,
                'intervals'=>$intervals,
            ];
        }

        // 8. Kiểm concurrency toàn khoảng [start,end)
        if (!$this->checkConcurrent($date, $appointmentStart, $appointmentEnd, $doctorCount)) {
            $suggestions = $this->suggestSlotsAcrossShifts(
                $date,
                $estimatedDuration,
                $maxSuggestions,
                $appointmentStart
            );
            $intervals = $this->groupContinuousSuggestions($suggestions);
            usort($intervals, function($a, $b) {
                [$h1,$m1] = explode(':',$a['start']);
                [$hh1,$mm1] = explode(':',$a['end']);
                $len1 = intval($hh1)*60 + intval($mm1) - (intval($h1)*60 + intval($m1));
                [$h2,$m2] = explode(':',$b['start']);
                [$hh2,$mm2] = explode(':',$b['end']);
                $len2 = intval($hh2)*60 + intval($mm2) - (intval($h2)*60 + intval($m2));
                return $len2 <=> $len1;
            });
            return [
                'available'=>false,
                'reason'=>'Concurrent overload at requested time range',
                'suggestions'=>$suggestions,
                'intervals'=>$intervals,
            ];
        }
     
        // 9. Nếu pass tất cả
        return [
            'available'=>true,
            'start_time'=>$startTime,
            'end_time'=>$appointmentEnd->format('H:i'),
        ];
    }

    /**
     * Hàm phụ: kiểm kết hợp workload tổng & concurrency peak trong từng sub-block
     *
     * @param string $date
     * @param Carbon $slotStart
     * @param Carbon $slotEnd
     * @param int    $doctorCount
     * @param int    $bufferMinutes
     * @param int    $subBlockMinutes
     * @param array  $existingAll      // array of ['appointmentTime'=>..., 'estimated_duration'=>...]
     * @param Carbon $shiftStart
     * @param Carbon $shiftEnd
     * @return array ['success'=>bool, 'fail_subblock'=>string|null]
     */
    protected function checkWorkloadAndConcurrencyInSubBlocks(
        string $date,
        Carbon $slotStart,
        Carbon $slotEnd,
        int $doctorCount,
        int $bufferMinutes,
        int $subBlockMinutes,
        array $existingAll,
        Carbon $shiftStart,
        Carbon $shiftEnd
    ): array {
        $blockCursor = $shiftStart->copy();
        while ($blockCursor->lt($shiftEnd)) {
            $blockEnd = $blockCursor->copy()->addMinutes($subBlockMinutes);
            if ($blockEnd->gt($shiftEnd)) {
                $blockEnd = $shiftEnd->copy();
            }
            if ($blockCursor->gte($blockEnd)) {
                $blockCursor = $blockEnd->copy();
                continue;
            }

            // Kiểm overlap slot mới với sub-block
            if (!($slotEnd->lte($blockCursor) || $slotStart->gte($blockEnd))) {
                // Phần overlap
                $ovStart = $slotStart->greaterThan($blockCursor) ? $slotStart : $blockCursor;
                $ovEnd   = $slotEnd->lessThan($blockEnd) ? $slotEnd : $blockEnd;
                // Tính newOverlap đúng (>=0)
                if ($ovEnd->gt($ovStart)) {
                    $newOverlap = $ovEnd->diffInMinutes($ovStart, false);
                } else {
                    $newOverlap = 0;
                }
                // 7.1 Tổng workload existing trong sub-block
                $existingOverlapSum = 0;
                $existingSub = [];
                foreach ($existingAll as $ex) {
                    $exStart = Carbon::parse("$date {$ex['appointmentTime']}");
                    $exEnd   = $exStart->copy()->addMinutes($ex['estimated_duration'] + $bufferMinutes);
                    if ($exEnd->lte($blockCursor) || $exStart->gte($blockEnd)) {
                        continue;
                    }
                    $eOvStart = $exStart->greaterThan($blockCursor) ? $exStart : $blockCursor;
                    $eOvEnd   = $exEnd->lessThan($blockEnd) ? $exEnd : $blockEnd;
                    if ($eOvEnd->gt($eOvStart)) {
                        $existingOverlapSum += $eOvEnd->diffInMinutes($eOvStart, false);
                    }
                    $existingSub[] = ['start'=>$exStart, 'end'=>$exEnd];
                }
                $capacity = $doctorCount * $blockCursor->diffInMinutes($blockEnd);
                if ($existingOverlapSum + $newOverlap > $capacity) {
                    $failSb = sprintf("%s–%s", $blockCursor->format('H:i'), $blockEnd->format('H:i'));
                    return ['success'=>false, 'fail_subblock'=>$failSb];
                }
                // 7.2 Concurrency peak trong sub-block (sweep-line)
                $events = [];
                // Event ca mới
                $events[] = ['time'=>$ovStart, 'delta'=>1];
                $events[] = ['time'=>$ovEnd,   'delta'=>-1];
                // Event existing
                foreach ($existingSub as $es) {
                    $eStart = $es['start']->greaterThan($blockCursor) ? $es['start'] : $blockCursor;
                    $eEnd   = $es['end']->lessThan($blockEnd) ? $es['end'] : $blockEnd;
                    if ($eEnd->gt($eStart)) {
                        $events[] = ['time'=>$eStart, 'delta'=>1];
                        $events[] = ['time'=>$eEnd,   'delta'=>-1];
                    }
                }
                usort($events, function($a, $b) {
                    if ($a['time']->eq($b['time'])) {
                        // nếu cùng thời điểm: đặt +1 trước -1 để tính concurrency đúng
                        return $a['delta'] < $b['delta'] ? -1 : 1;
                    }
                    return $a['time']->lt($b['time']) ? -1 : 1;
                });
                $cur = 0;
                foreach ($events as $ev) {
                    $cur += $ev['delta'];
                    if ($cur > $doctorCount) {
                        $failSb = sprintf("%s–%s", $blockCursor->format('H:i'), $blockEnd->format('H:i'));
                        return ['success'=>false, 'fail_subblock'=>$failSb];
                    }
                }
            }
            $blockCursor = $blockEnd->copy();
        }
        return ['success'=>true, 'fail_subblock'=>null];
    }

    /**
     * Kiểm concurrency tổng toàn khoảng [start, end): đảm bảo tại mọi thời điểm concurrent <= doctorCount
     *
     * @param string $date  // "YYYY-MM-DD"
     * @param Carbon $start
     * @param Carbon $end
     * @param int    $doctorCount
     * @return bool
     */
    protected function checkConcurrent(string $date, Carbon $start, Carbon $end, int $doctorCount): bool
    {
        $bufferMinutes = 10;
        $existing = Appointment::where('status','Confirmed')
            ->whereDate('bookingDate',$date)
            ->where(function($q) use($start,$end,$bufferMinutes) {
                $q->whereRaw(
                    '(appointmentTime < ?) AND (DATE_ADD(appointmentTime, INTERVAL estimated_duration + ? MINUTE) > ?)',
                    [$end->format('H:i:s'), $bufferMinutes, $start->format('H:i:s')]
                );
            })
            ->get(['appointmentTime','estimated_duration']);

        $events = [];
        // Event appointment mới
        $events[] = ['time'=>$start, 'delta'=>1];
        $events[] = ['time'=>$end,   'delta'=>-1];
        // Event existing
        foreach ($existing as $ex) {
            $exStart = Carbon::parse($date.' '.$ex->appointmentTime);
            $exEnd   = $exStart->copy()->addMinutes($ex->estimated_duration + $bufferMinutes);
            if ($exEnd->lte($start) || $exStart->gte($end)) {
                continue;
            }
            $events[] = ['time'=>$exStart, 'delta'=>1];
            $events[] = ['time'=>$exEnd,   'delta'=>-1];
        }
        usort($events, function($a, $b) {
            if ($a['time']->eq($b['time'])) {
                return $a['delta'] < $b['delta'] ? -1 : 1;
            }
            return $a['time']->lt($b['time']) ? -1 : 1;
        });
        $cur = 0;
        foreach ($events as $ev) {
            $cur += $ev['delta'];
            if ($cur > $doctorCount) {
                return false;
            }
        }
        return true;
    }

    /**
     * Gợi ý slot trong 1 shift với đúng doctorCount cho shift đó
     *
     * @param string $date
     * @param int    $estimatedDuration (phút, chưa cộng buffer)
     * @param Carbon $shiftStart
     * @param Carbon $shiftEnd
     * @param int    $doctorCount
     * @param int    $maxSuggestions
     * @param Carbon|null $originalStart
     * @return array [ ['start_time'=>'HH:mm','end_time'=>'HH:mm'], ... ]
     */
    protected function suggestSlotsWithinShift(
        string $date,
        int $estimatedDuration,
        Carbon $shiftStart,
        Carbon $shiftEnd,
        int $doctorCount,
        int $maxSuggestions,
        ?Carbon $originalStart = null
    ): array {
        $suggestions = [];
        $slotLength = 15;
        $bufferMinutes = 10;
        $durationWithBuffer = $estimatedDuration + $bufferMinutes;

        $cursor = $shiftStart->copy();
        $latestStart = $shiftEnd->copy()->subMinutes($durationWithBuffer);
        if ($latestStart->lt($shiftStart)) {
            return [];
        }

        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        // Preload existing trong shift (có thể cache)
        $existingAll = Appointment::where('status','Confirmed')
            ->whereDate('bookingDate',$date)
            ->get(['appointmentTime','estimated_duration'])
            ->toArray();

        $subBlockMinutes = 60;

        while ($cursor->lte($latestStart) && count($suggestions) < $maxSuggestions) {
            $slotStart = $cursor->copy();
            $slotEnd   = $slotStart->copy()->addMinutes($durationWithBuffer);

            // Nếu ngày hiện tại và slotEnd đã qua, skip
            if ($date === $today && $slotEnd->lte($now)) {
                $cursor->addMinutes($slotLength);
                continue;
            }
            // Skip originalStart khi update
            if ($originalStart && $slotStart->eq($originalStart)) {
                $cursor->addMinutes($slotLength);
                continue;
            }
            // Nếu muốn lấy số bác sĩ động trong shift, gọi lại:
            $dc = $this->getDoctorCountByShift($date, $slotStart->format('H:i'));
            if ($dc <= 0) {
                $cursor->addMinutes($slotLength);
                continue;
            }
            // Concurrency tổng
            if (!$this->checkConcurrent($date, $slotStart, $slotEnd, $dc)) {
                $cursor->addMinutes($slotLength);
                continue;
            }
            // Workload+concurrency per sub-block
            $combinedCheck = $this->checkWorkloadAndConcurrencyInSubBlocks(
                $date,
                $slotStart,
                $slotEnd,
                $dc,
                $bufferMinutes,
                $subBlockMinutes,
                $existingAll,
                $shiftStart,
                $shiftEnd
            );
            if (!$combinedCheck['success']) {
                $cursor->addMinutes($slotLength);
                continue;
            }
            // Pass cả
            $suggestions[] = [
                'start_time' => $slotStart->format('H:i'),
                'end_time'   => $slotEnd->format('H:i'),
            ];
            $cursor->addMinutes($slotLength);
        }
        return $suggestions;
    }

    /**
     * Gợi ý across shifts, lấy đúng doctorCount mỗi shift
     *
     * @param string $date
     * @param int    $estimatedDuration
     * @param int    $maxSuggestions
     * @param Carbon|null $originalStart
     * @return array
     */
    protected function suggestSlotsAcrossShifts(
        string $date,
        int $estimatedDuration,
        int $maxSuggestions,
        ?Carbon $originalStart = null
    ): array {
        $suggestions = [];
        // Danh sách ca cố định; nếu ca thay đổi theo config, có thể load từ DB
        $shifts = [
            ['name' => 'Morning Shift',   'start' => '08:00', 'end' => '11:30'],
            ['name' => 'Afternoon Shift', 'start' => '13:30', 'end' => '17:30'],
            ['name' => 'Evening Shift',   'start' => '17:30', 'end' => '21:00'],
        ];
        foreach ($shifts as $shift) {
            if (count($suggestions) >= $maxSuggestions) {
                break;
            }
            $shiftStart = Carbon::parse("$date {$shift['start']}");
            $shiftEnd   = Carbon::parse("$date {$shift['end']}");
            // Lấy đúng số bác sĩ cho shift này
            $doctorCountForShift = $this->getDoctorCountByShift($date, $shift['start']);
            if ($doctorCountForShift <= 0) {
                continue;
            }
            $slots = $this->suggestSlotsWithinShift(
                $date,
                $estimatedDuration,
                $shiftStart,
                $shiftEnd,
                $doctorCountForShift,
                $maxSuggestions - count($suggestions),
                $originalStart
            );
            $suggestions = array_merge($suggestions, $slots);
        }
        return $suggestions;
    }

    /**
     * Group các slot liên tục/chồng lắp thành khoảng rộng
     *
     * @param array $suggestions [ ['start_time'=>'HH:mm','end_time'=>'HH:mm'], ... ]
     * @return array [ ['start'=>'HH:mm','end'=>'HH:mm'], ... ]
     */
    protected function groupContinuousSuggestions(array $suggestions): array
    {
        if (empty($suggestions)) return [];
        // Sort theo start_time
        usort($suggestions, function($a, $b) {
            return strcmp($a['start_time'], $b['start_time']);
        });
        $grouped = [];
        $current = null;
        foreach ($suggestions as $slot) {
            if (!$current) {
                $current = ['start'=>$slot['start_time'], 'end'=>$slot['end_time']];
                continue;
            }
            // Nếu slot mới bắt đầu <= current['end'], nối
            if ($slot['start_time'] <= $current['end']) {
                // Mở rộng end nếu cần
                if ($slot['end_time'] > $current['end']) {
                    $current['end'] = $slot['end_time'];
                }
            } else {
                // Không nối: push current, reset
                $grouped[] = $current;
                $current = ['start'=>$slot['start_time'], 'end'=>$slot['end_time']];
            }
        }
        if ($current) {
            $grouped[] = $current;
        }
        return $grouped;
    }

    /**
     * Xác định shift (ca làm) dựa trên startTime
     * @param string $date     // "YYYY-MM-DD"
     * @param string $startTime// "HH:mm"
     * @return array|null ['shiftStart'=>Carbon,'shiftEnd'=>Carbon,'shiftId'=>int] hoặc null nếu không trong ca
     */
    protected function getShiftRange(string $date, string $startTime): ?array
    {
        $time = Carbon::parse("$date $startTime");
        // Ca sáng: 08:00–12:00
        $mornStart = Carbon::parse("$date 08:00");
        $mornEnd   = Carbon::parse("$date 12:00");
        if ($time->between($mornStart, $mornEnd, true)) {
            return ['shiftStart'=>$mornStart,'shiftEnd'=>$mornEnd,'shiftId'=>1];
        }
        // Ca chiều: 13:30–17:30
        $aftStart = Carbon::parse("$date 13:30");
        $aftEnd   = Carbon::parse("$date 17:30");
        if ($time->between($aftStart, $aftEnd, true)) {
            return ['shiftStart'=>$aftStart,'shiftEnd'=>$aftEnd,'shiftId'=>2];
        }
        // Ca tối: 17:30–21:00 (nếu có)
        $eveStart = Carbon::parse("$date 17:30");
        $eveEnd   = Carbon::parse("$date 21:00");
        if ($time->between($eveStart, $eveEnd, true)) {
            return ['shiftStart'=>$eveStart,'shiftEnd'=>$eveEnd,'shiftId'=>3];
        }
        return null;
    }

    /**
     * Đếm số bác sĩ trực đủ cover toàn khoảng appointment dựa vào ca (shift) của startTime
     * @param string $date  // "YYYY-MM-DD"
     * @param string $time  // "HH:mm"
     * @return int
     */
    public function getDoctorCountByShift(string $date, string $time): int
    {
         
    try {
        $carbonTime = Carbon::parse($time);
    } catch (\Exception $e) {
        Log::warning("countDoctorsByShift: cannot parse time '$time'");
        return 0;
    }
    $hm = $carbonTime->format('H:i');
    $shiftId = null;
    if ($hm >= '08:00' && $hm < '11:30') {
        $shiftId = 1; // Ca sáng
    } elseif ($hm >= '13:30' && $hm < '17:30') {
        $shiftId = 2; // Ca chiều
    } elseif ($hm >= '17:30' && $hm < '23:00') {
        $shiftId = 3; // Ca tối
    }
    if (!$shiftId) {
        return 0;
    }

    $query = WorkSchedule::whereDate('registerDate', $date)
        ->whereHas('workScheduleDetails', function ($q) use ($shiftId) {
            $q->where('shiftId', $shiftId)
              ->where('status', 'working');
        })
        ->whereHas('employee', function ($q) {
            $q->where('status', 'working')
              ->where('role', 'Doctor');
      
        });

    // Cuối cùng count()
    $count = $query->count();

    return $count;
    }
 

    
}  

   

  
