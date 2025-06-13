<?php
namespace App\Repositories;

use App\Models\WorkSchedule;
use Illuminate\Pagination\LengthAwarePaginator;

class WorkScheduleRepository extends BaseRepository
{
    function getModel()
    {
        return WorkSchedule::class;
    }

    public function getWorkSchedules($input)
{
    $weekStart = $input['weekStart'];
    $weekEnd = $input['weekEnd'];
    $name = $input['keyword'];
    $pageSize = $input['pageSize'] ?? 5; // Mặc định 5, có thể thay bằng 2 cho ví dụ
    $employeeId = $input['employeeId'] ?? null;

    $query = WorkSchedule::with(['employee', 'workScheduleDetails.workShift']);

    // Chỉ lọc theo employeeId nếu có
    if ($employeeId) {
        $query->where('idEmployee', $employeeId);
    }

    // Tìm kiếm theo tên nhân viên
    if ($name) {
        $query->whereHas('employee', function ($q) use ($name) {
            $q->where('fullName', 'like', "%{$name}%");
        });
    }

    // Lọc theo khoảng thời gian
    if ($weekStart && $weekEnd) {
        $query->whereBetween('registerDate', [$weekStart, $weekEnd]);
    }

    $workSchedules = $query->orderBy('registerDate', 'desc')->get(); // Lấy toàn bộ dữ liệu tuần

    $transformed = $workSchedules->map(function ($schedule) {
        // Lấy thông tin chi tiết các ca làm việc
        $details = $schedule->workScheduleDetails->map(function ($detail) {
            if ($detail->workShift) {
                return [
                    'shiftId' => $detail->shiftId,
                    'shiftName' => $detail->workShift->shiftName,
                    'time' => $detail->workShift->startTime . '-' . $detail->workShift->endTime,
                    'status' => $detail->status
                ];
            }
            return null;
        })->filter();

        // Nhóm các ca theo trạng thái
        $workingShifts = $details->where('status', 'working')
            ->map(fn($shift) => $shift['time'])
            ->join(', ');
        
        $offShifts = $details->where('status', 'off')
            ->map(fn($shift) => $shift['time'])
            ->join(', ');

        return [
            "id" => $schedule->id,
            "employeeId" => $schedule->idEmployee,
            "name" => optional($schedule->employee)->fullName,
            "registerDate" => $schedule->registerDate,
            "workingShifts" => $workingShifts ?: "No working shifts",
            "offShifts" => $offShifts ?: "No off shifts",
            "allShifts" => $details->toArray(),
            "employee" => [
                "id" => optional($schedule->employee)->id,
                "fullName" => optional($schedule->employee)->fullName,
                "email" => optional($schedule->employee)->email,
                "phone" => optional($schedule->employee)->phone
            ]
        ];
    });

    // Trả về toàn bộ dữ liệu và thông tin tổng quát
    return [
        'data' => $transformed->values()->all(),
        'total' => $transformed->count(),
        'current_page' => 1, // Frontend sẽ xử lý phân trang theo ngày
        'per_page' => $pageSize,
        'last_page' => ceil($transformed->count() / $pageSize) // Tổng số trang nếu áp dụng trên toàn bộ
    ];
}
    public function getDoctorsByShiftWithExamCount($date, $time)
    {
        // Xác định shiftId dựa trên thời gian
        $shiftId = null;
        if ($time >= 7 && $time < 13) {
            $shiftId = 1; // Ca sáng
        } elseif ($time >= 13 && $time < 17) {
            $shiftId = 2; // Ca chiều
        } elseif ($time >= 17 && $time < 23) {
            $shiftId = 3; // Ca tối
        }
    
        if (!$shiftId) {
            return collect([]); // Trả về collection rỗng
        }
    
        // Truy vấn danh sách bác sĩ làm việc trong ca + Đếm medicalExams có status = 'pending'
        $doctors = WorkSchedule::with([
            'employee' => function ($query) {
                $query->where('status', 'working') // Thêm điều kiện lọc theo status của employee
                      ->withCount([
                          'medicalExams as pending_exams_count' => function ($q) {
                              $q->where('status', 'pending');
                          }
                      ]);
            },
            'workScheduleDetails.workShift'
        ])
        ->where('registerDate', $date)
        ->whereHas('workScheduleDetails', function ($q) use ($shiftId) {
            $q->where('shiftId', $shiftId)
              ->where('status', 'working');
        })
        ->get()
        ->filter(fn($schedule) => $schedule->employee !== null) // Đảm bảo chỉ lấy các bác sĩ còn tồn tại sau khi lọc status
        ->sortBy(fn($schedule) => $schedule->employee->pending_exams_count ?? 0)
        ->pluck('employee');
    
        
    
        return $doctors;
    }
    
    public function getByDateAndEmployee($date, $employeeId)
{
    return WorkSchedule::whereDate('registerDate', $date)
        ->where('idEmployee', $employeeId)
        ->first();
}

    public function getByDateRangeAndEmployee($startDate, $endDate, $employeeId)
    {
        return WorkSchedule::where('idEmployee', $employeeId)
            ->whereBetween('registerDate', [$startDate, $endDate])
            ->exists();
    }
}