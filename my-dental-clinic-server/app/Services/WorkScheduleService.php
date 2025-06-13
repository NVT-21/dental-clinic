<?php 
namespace App\Services;

use App\Repositories\WorkScheduleRepository;
use App\Repositories\WorkScheduleDetailRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\MedicalExam;
use App\Models\Employee;
use App\Models\Notification;
use App\Events\DoctorShiftChanged;
use Illuminate\Support\Facades\Log;
use Throwable; 

class WorkScheduleService extends BaseService
{
    protected $WorkScheduleRepository;
    protected $WorkScheduleDetailRepository;
    public function __construct(WorkScheduleRepository $WorkScheduleRepository,WorkScheduleDetailRepository $WorkScheduleDetailRepository)
    {
        $this->WorkScheduleRepository = $WorkScheduleRepository;
        $this->WorkScheduleDetailRepository = $WorkScheduleDetailRepository;
        parent::__construct();
    }
    public function getRepository()
    {
        return $this->WorkScheduleRepository;
    }
    public function createOrUpdateWorkSchedule($idEmployee, $data, $manager)
    {
        try {
            return DB::transaction(function () use ($idEmployee, $data, $manager) {
                $results = [];
                $examsToReassign = [];
                // Kiểm tra xem là update một ngày hay tạo mới theo tuần
                $isSingleDayUpdate = isset($data[0]['id']) && !empty($data[0]['id']);

                if ($isSingleDayUpdate) {
                    // Xử lý update một ngày
                    $workSchedule = $this->WorkScheduleRepository->getById($data[0]['id']);
                    if (!$workSchedule) {
                        return [
                            "success" => false,
                            "message" => "Work schedule not found!"
                        ];
                    }

                    // Cập nhật trạng thái các ca
                    foreach ($data[0]['status'] as $shiftId => $status) {
                        $detail = $this->WorkScheduleDetailRepository->getByScheduleAndShift($workSchedule->id, $shiftId);
                        $oldStatus = $detail ? $detail->status : null;
                        $newStatus = $status;
                        
                        if ($detail) {
                            $this->WorkScheduleDetailRepository->updateWorkScheduleDetail($workSchedule->id, $shiftId, $newStatus);
                        } 

                        // Kiểm tra nếu chuyển từ working sang off
                        if ($oldStatus === 'working' && $newStatus === 'off') {
                            $exams = MedicalExam::where('idEmployee', $idEmployee)
                                ->whereDate('ExamDate', $workSchedule->registerDate)
                                ->whereIn('status', ['Pending', 'In Progress'])
                                ->get();

                            if ($exams->count() > 0) {
                                MedicalExam::where('idEmployee', $idEmployee)
                                    ->whereDate('ExamDate', $workSchedule->registerDate)
                                    ->whereIn('status', ['pending', 'In Progress'])
                                    ->update(['status' => 'Needs Reassign']);
                                    $doctor = Employee::find($idEmployee);
                                    $message = $exams->count() . " medical exam(s) for Dr. {$doctor->fullName} need reassignment due to shift changes.";
                                    Notification::create([
                                        'idEmployee' => $manager->id,
                                        'message' => $message,
                                        'created_at' => now(),
                                        'read_at' => null,
                                    ]);
                                    event(new DoctorShiftChanged($manager->id, $workSchedule->registerDate, $exams->count(), $doctor->fullName));
                            }
                        }
                    }

                    $results[] = [
                        'date' => $workSchedule->registerDate,
                        'success' => true,
                        'workSchedule' => $workSchedule
                    ];
                } else {
                    // Xử lý tạo mới theo tuần
                    foreach ($data as $scheduleData) {
                        $registerDate = $scheduleData['registerDate'];
                        $status = $scheduleData['status'];

                        // Kiểm tra xem đã có lịch cho ngày này chưa
                   
                            // Tạo lịch mới
                            $workSchedule = $this->WorkScheduleRepository->create([
                                'registerDate' => $registerDate,
                                'idEmployee' => $idEmployee
                            ]);

                            // Tạo chi tiết các ca làm việc
                            foreach ($status as $shiftId => $isWorking) {
                                $this->WorkScheduleDetailRepository->create([
                                    'workScheduleId' => $workSchedule->id,
                                    'shiftId' => $shiftId,
                                    'status' => $isWorking ? 'working' : 'off',
                                ]);
                            }
                        

                        $results[] = [
                            'date' => $registerDate,
                            'success' => true,
                            'workSchedule' => $workSchedule
                        ];
                    }
                }

           
                return [
                    "success" => true,
                    "message" => $isSingleDayUpdate ? "Work schedule updated successfully" : "Work schedules created successfully",
                    "results" => $results
                ];
            });
        } catch (\Throwable $e) {
            Log::error("Error creating/updating work schedules: " . $e->getMessage());
            return [
                "success" => false,
                "message" => "Failed to create/update work schedules: " . $e->getMessage()
            ];
        }
    }
   public function workSchedules($input)
   {
    return $this->WorkScheduleRepository->getWorkSchedules($input);
   }
   public function getDoctorWorking($date,$time)
   {
    return $this->WorkScheduleRepository->getDoctorsByShiftWithExamCount($date,$time);
   }
   public function checkExistingSchedulesInWeek($employeeId, $startDate, $endDate)
   {
        return $this->WorkScheduleRepository->getByDateRangeAndEmployee($startDate, $endDate, $employeeId);
   }
}