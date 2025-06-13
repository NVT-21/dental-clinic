<?php

namespace App\Repositories;
use App\Models\Employee;
 class EmployeeRepository extends BaseRepository
 {
    function getModel(){
        return Employee::class ;
    }
    public function getDoctorsWithoutRoom()
    {
        return Employee::whereNull('idRoom')
            ->where('role', 'Doctor') 
            ->get();
    }
    public function getRoomOfDoctor($idDoctor){
        $employee = Employee::with("room")->where('id', $idDoctor)->first();

    if (!$employee) {
        return response()->json(["message" => "Doctor not found"], 404);
    }

    return response()->json($employee->room, 200);
    }
   public function getEmployees($input)
{
    $query = Employee::query()
        ->with(['user.roles' => function ($query) {
            $query->select('name'); // Chỉ lấy tên vai trò
        }]);

    // Loại bỏ nhân viên có vai trò Administrator
    $query->whereDoesntHave('user.roles', function ($query) {
        $query->where('name', 'Administrator');
    });

    if (!empty($input['keyword'])) {
        $query->where('fullName', 'LIKE', '%' . $input['keyword'] . '%');
    }

    if (!empty($input['status']) && $input['status'] !== 'all') {
        $query->where('status', $input['status']);
    }

    $perPage = $input['per_page'] ?? 10;
    $employees = $query->orderBy('created_at', 'desc')->paginate($perPage);

    // Thêm trường roles vào dữ liệu trả về
    $employees->getCollection()->transform(function ($employee) {
        $employee->roles = $employee->user->roles->pluck('name')->toArray();
        return $employee;
    });

    return $employees;
}
    
  
 }