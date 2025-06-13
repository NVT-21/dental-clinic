<?php

namespace App\Http\Controllers;
use App\Services\WorkScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WorkScheduleController extends ApiResponseController
{
    protected $WorkScheduleService ;
    public function __construct(WorkScheduleService $WorkScheduleService )
    {
        $this->WorkScheduleService = $WorkScheduleService;
    }
    public function createOrUpdate(Request $request)
    {   
        $employee = $this->getEmployee();
        if(!$employee)
        {
            return $this->error("Not Found Employee");
        }

        $data = $request->all();
        $idEmployee = null;
        // Nếu là update một ngày, lấy idEmployee từ lịch làm việc
        if (isset($data[0]['id'])&&!empty($data[0]["id"])) {
            $workSchedule = $this->WorkScheduleService->getById($data[0]['id']);
            if (!$workSchedule) {
                return $this->error("Work schedule not found!");
            }
            $idEmployee = $workSchedule->idEmployee;
        } else {
            // Kiểm tra nếu là mảng dữ liệu tuần (tạo mới theo tuần)
            if (is_array($data) && isset($data[0]['registerDate'])) {
                // Lấy ngày đầu và cuối của tuần
                $firstDate = $data[0]['registerDate'];
                $lastDate = end($data)['registerDate'];
                
                // Kiểm tra xem đã có lịch trong tuần này chưa
                $existingSchedules = $this->WorkScheduleService->checkExistingSchedulesInWeek($employee->id, $firstDate, $lastDate);
                if ($existingSchedules) {
                    return $this->error("Work schedules already exist for this week.");
                }
            }
            $idEmployee = $employee->id;
        }
    
        $result = $this->WorkScheduleService->createOrUpdateWorkSchedule($idEmployee, $data, $employee);
        if ($result['success']) {
            return $this->success($result['message']);
        } else {
            return $this->error($result['message']);
        }
    }
    public function paging(Request $request)
{
    $input = [
        'page' => $request->input('page', 1),
        'pageSize' => $request->input('pageSize', 10),
        'weekStart' => $request->input('weekStart'), // Tuần bắt đầu
        'weekEnd' => $request->input('weekEnd'),    // Tuần kết thúc
        'keyword' => $request->input('keyword'),
    ];

    // Lấy thông tin người dùng đã đăng nhập
    $user = $this->getUser();
    if ($user) {
        $roles = $user->roles->pluck('name')->toArray();
        if (in_array('Doctor', $roles) || in_array('Receptionist', $roles)) {
            $employee = $user->employee;
            if ($employee) {
                $input['employeeId'] = $employee->id;
            }
        } elseif (!in_array('Administrator', $roles) && !in_array('Manager', $roles)) {
            // Nếu không phải Admin/Manager và không phải Doctor/Receptionist, không hiển thị gì
            $input['employeeId'] = null; // Có thể thêm logic khác nếu cần
        }
    }

    return $this->WorkScheduleService->workSchedules($input);
}
    public function getDoctorWorking()
    {
        $date = Carbon::now()->format('Y-m-d'); 
        $time = Carbon::now()->hour;
        return $this->WorkScheduleService->getDoctorWorking($date,$time);
    }
}
