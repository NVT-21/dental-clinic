<?php

namespace App\Repositories;
use App\Models\MedicalExam;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Employee;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\PatientAssigned;

class MedicalExamRepository extends BaseRepository
{
    function getModel(){
        return MedicalExam::class ;
    }
    public function createMedicalExam($data,$creator)
    {
        DB::beginTransaction();
        try {
            $phoneNumber = $data['phoneNumber'];
            $name = $data['fullname'];

            // Tìm bệnh nhân theo số điện thoại và tên
            $patient = Patient::where('phoneNumber', $phoneNumber)
                              ->where('fullname', $name)
                              ->first();

            if (!$patient) {
                // Nếu không tìm thấy, tạo bệnh nhân mới
                $patient = Patient::create([
                    'fullname' => $name,
                    'phoneNumber' => $phoneNumber,
                    'email' => $data['email'] ?? null,
                    'birthdate' => $data['birthdate'] ?? null,
                ]);
            }

            // Kiểm tra xem cuộc hẹn đã tồn tại chưa
            $appointment = null;
            if (!empty($data['idAppointment'])) {
                $appointment = Appointment::where('id', $data['idAppointment'])->first();
                if (!$appointment) {
                    throw new \Exception("Appointment not found with ID: " . $data['idAppointment']);
                }

                // Tính toán trạng thái đến nếu là appointment đã xác nhận
                if ($appointment->status === 'Confirmed') {
                    $now = now();
                    $appointmentTime = Carbon::parse($appointment->appointmentTime);
                    $diffInMinutes = $now->diffInMinutes($appointmentTime, false);
                    
                    if ($diffInMinutes < -15) { // Đến muộn hơn 15 phút
                        $appointment->arrival_status = 'late';
                    } elseif ($diffInMinutes > 15) { // Đến sớm hơn 15 phút
                        $appointment->arrival_status = 'early';
                    } else {
                        $appointment->arrival_status = 'on_time';
                    }
                }
            }

            if (!$appointment) {
                // Nếu chưa có, tạo cuộc hẹn mới
                $appointment = Appointment::create([
                    'idPatient' => $patient->id,
                    'bookingDate' => null,
                    'appointmentTime' => null,
                    'status' => null,
                    'is_done' => true,
                    'arrival_status' => null // Không cần xác định trạng thái đến cho khám tự do
                ]);
            } else {
                $appointment->is_done = true;
                $appointment->save();
            }

            // kiem tra xem co ca kham nao cua benh nhan dang ton tai không
            $existingExam = MedicalExam::whereHas('appointment', function ($query) use ($patient) {
                    $query->where('idPatient', $patient->id);
                })
                ->whereIn('status', ['Pending', 'In Progress','Needs Reassign'])
                ->where('idAppointment', '!=', $appointment->id) // Loại trừ cuộc hẹn hiện tại (trong chế độ update)
                ->first();

                if ($existingExam) {
                    throw new \Exception("Patient already has an active medical exam (pending or in progress).");
                }
            // Kiểm tra doctorId (nếu có) có tồn tại trong bảng Employee không
            if (isset($data['doctorId'])) {
                $doctor = Employee::find($data['doctorId']);
                if (!$doctor) {
                    throw new \Exception("Doctor not found with ID: " . $data['doctorId']);
                }
            }

            // Kiểm tra xem MedicalExam đã tồn tại chưa
            $medicalExam = MedicalExam::where('idAppointment', $appointment->id)->first();

            if ($medicalExam) {
                // Cập nhật MedicalExam hiện có
                $newDoctorId = $data['doctorId'] ?? $medicalExam->idEmployee;
                if ($newDoctorId !== $medicalExam->idEmployee) {
                    $data['status'] = $medicalExam->status === 'In Progress' ? 'In Progress' : 'Pending';
                } else {
                    $data['status'] = $data['status'] ?? $medicalExam->status;
                }
                $oldDoctorId=$medicalExam->idEmployee;
                $medicalExam->update([
                    'idEmployee' => $data['doctorId'] ?? $medicalExam->idEmployee, // Giữ nguyên nếu không có doctorId mới
                    'symptoms' => $data['symptoms'] ?? $medicalExam->symptoms,
                    'status' => $data['status'] ?? 'Pending',
                ]);
                $message = "Medical exam updated successfully";
                // Tạo thông báo cho bác sĩ nếu có doctorId mới
               // Tạo thông báo cho bác sĩ nếu có doctorId mới
               if ($newDoctorId !== $oldDoctorId) {
                $doctor = Employee::find($newDoctorId);
                $notificationMessage = "Patient {$patient->fullname} has been reassigned to you, Dr. {$doctor->fullName}, to continue the examination!";
                Log::info('Dispatching PatientAssigned event for update', [
                    'patientName' => $patient->fullname,
                    'doctorName' => $doctor->fullName,
                    'doctorId' => $newDoctorId,
                    'isNew' => false
                ]);

                // Create notification in database
                Notification::create([
                    'idEmployee' => $newDoctorId,
                    'message' => $notificationMessage,
                    'patient_name' => $patient->fullname,
                    'doctor_name' => $doctor->fullName,
                    'created_at' => now(),
                    'read_at' => null, // Initially unread
                ]);

                event(new PatientAssigned($patient->fullname, $doctor->fullName, $newDoctorId, false));
            }
            } else {
                // Tạo mới MedicalExam
                if (!isset($data['doctorId'])) {
                    throw new \Exception("Doctor ID is required for creating a new medical exam");
                }
                $medicalExam = MedicalExam::create([
                    'idEmployee' => $data['doctorId'],
                    'idAppointment' => $appointment->id,
                    'createdById'=>$creator->id,
                    'status'=> 'Pending',
                    'symptoms' => $data['symptoms'] ?? null,
                    'ExamDate' => Carbon::now()->toDateString()
                ]);
                $message = "Medical exam created successfully";
                $doctor = Employee::find($data['doctorId']);
                $notificationMessage = "Patient {$patient->fullname} has been assigned to you, Dr. {$doctor->fullName}, for a new examination!";
                  
                Log::info('Dispatching PatientAssigned event for new assignment', [
                    'patientName' => $patient->fullname,
                    'doctorName' => $doctor->fullName,
                    'doctorId' => $data['doctorId'],
                    'isNew' => true
                ]);

                // Create notification in database
                Notification::create([
                    'idEmployee' => $data['doctorId'],
                    'message' => $notificationMessage,
                    'patient_name' => $patient->fullname,
                    'doctor_name' => $doctor->fullName,
                    'created_at' => now(),
                    'read_at' => null, // Initially unread
                ]);
                event(new PatientAssigned($patient->fullname, $doctor->fullName, $data['doctorId'], true));
            }

            // Cập nhật is_done = true cho appointment
            $appointment->is_done = true;
            $appointment->save();

            DB::commit();

            return [
                "success" => true,
                "message" => $message,
                "data" => $medicalExam,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return [
                "success" => false,
                "message" => "Failed to create or update medical exam: " . $e->getMessage(),
            ];
        }
    }
    public function getMedicalExam($perPage = 5, $status = "Pending", $statusPayment, $idEmployee)
    {
        $query = MedicalExam::with(['appointment.patient', 'employee'])
            ->leftJoin('appointments', 'medical_exams.idAppointment', '=', 'appointments.id')
            ->orderByRaw("
                CASE 
                    -- Đặt lịch online & đến đúng giờ
                    WHEN appointments.status = 'Confirmed' 
                         AND appointments.arrival_status = 'on_time' 
                         AND appointments.is_done = true THEN 1
                    
                    -- Đặt lịch online nhưng đến sớm/muộn
                    WHEN appointments.status = 'Confirmed' 
                         AND appointments.arrival_status IN ('early', 'late')
                         AND appointments.is_done = true THEN 2
                    
                    -- Không đặt lịch (khám tự do)
                    WHEN appointments.status IS NULL 
                         AND appointments.is_done = true THEN 3
                END
            ")
            ->orderBy('medical_exams.created_at', 'asc') // Sắp xếp theo thời gian tạo ca khám (check-in)
            ->select('medical_exams.*');

        if ($status && $status !== "all") {
            $query->where('medical_exams.status', $status);
        }
        if ($statusPayment && $statusPayment !== 'all') {
            $query->where('medical_exams.statusPayment', $statusPayment);
        }
        if ($idEmployee) {
            $query->where('medical_exams.idEmployee', $idEmployee);
        }

        return $query->paginate($perPage);
    }
    public function saveDoctorConclusion(array $data)
    {
        // Validate presence of required fields
        if ( !isset($data['medical_exam_id'])) {
            throw new \InvalidArgumentException('Missing required data: medical_exam_id.');
        }

        // Find the medical exam by ID
        $exam = MedicalExam::findOrFail($data['medical_exam_id']);

        // Update the exam with diagnosis and advice
        $exam->diagnosis = $data['diagnosis'];
        $exam->advice = $data['advice'];
        $exam->save();

        return $exam;
    }
    public function getPrescriptionAndService($idMedicalExam)
    {
        try {
            $medicalExam = MedicalExam::findOrFail($idMedicalExam);

            $services = $medicalExam->services;

            $prescription = $medicalExam->prescription;
            $doctor=$medicalExam->employee;

            $medicines = $prescription ? $prescription->medicines : collect();

            return response()->json([
                'success' => true,
                'services' => $services,
                'medicines' => $medicines,  
                'doctor'=>$doctor
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medical exam not found.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    public function updateMedicalExam($id, $data)
    {
        DB::beginTransaction();
        try {
            $medicalExam = MedicalExam::findOrFail($id);
            $medicalExam->update($data);
            DB::commit();
            return [
                "success" => true,
                "message" => "Successfully updated",
                'data' => $medicalExam
            ];
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return [
                "success" => false,
                "message" => "Failed to update"
            ];
        }
    }
}