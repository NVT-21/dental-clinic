<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\EmployeeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class AuthController extends ApiResponseController
{
    protected $AuthService ;
    protected $EmployeeService ;
    public function __construct(AuthService $AuthService ,EmployeeService $EmployeeService)
    {
        $this->AuthService = $AuthService;
        $this->EmployeeService = $EmployeeService;
    }
    public function showLoginForm()
    {
        return view ('Auth.login');
    }
        public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $result = $this->AuthService->login($credentials);

        if ($result['success']) {
            
            return response()->json([
                'message' => $result['message'],
                'token' => $result['token'],
                'user' => $result['user'] // Trả về user
            ]);
        } else {
            return response()->json([
                'message' => $result['message']
            ], 401);
        }   
    }
    public function saveOrUpdateEmployee (Request $request)
    {
       
        DB::beginTransaction();
    
        try {
            $employeeId = $request->input('id'); // 👈 Nếu có ID thì là update

            if ($employeeId) {
                // Cập nhật nhân viên
                $employeeData = $request->only('fullName', 'phoneNumber', 'gender', 'birthday', 'status');
                
                $this->AuthService->updateEmployee($employeeId, $employeeData); // 👈 bạn cần có hàm này
    
                DB::commit();
                return response()->json(['message' => 'Employee updated successfully'], 200);
            }
            $credential = $request->only('email', 'password');
            $role = $request->only('role');
            $employee = $request->only('fullName', 'phoneNumber', 'gender', 'birthday');
            if ($this->AuthService->isEmail($credential['email'])) {
                return response()->json(['message' => 'Email already exists'], 400); 
            }
            // Gán vai trò cho user và tạo mới user
            $newUser = $this->AuthService->assignRole($credential, $role);
    
            if ($newUser) {
                // Thêm user_id vào mảng employee
                $employee['user_id'] = $newUser->id;
                $employee['role']=$this->roleNameById($role);
                // Tạo mới employee
                $this->EmployeeService->createEmployee($employee);
    
                // Commit transaction nếu mọi thứ thành công
                DB::commit();
    
                return response()->json(['message' => 'Successfully registered'], 200);
            } else {
                // Nếu không thể tạo user, rollback
                DB::rollBack();
                return response()->json(['message' => 'Registration failed'], 400);
            }
        } catch (\Exception $e) {
            // Nếu có lỗi xảy ra trong quá trình tạo user hoặc employee, rollback
            DB::rollBack();
    
            Log::error("Error during registration: " . $e->getMessage());
    
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }
    public function changePassword (Request $request)
    {
        $credential=$request->all();
        $result = $this->AuthService->changePassword($credential);
        if ($result['success']) {
            return $this->success($result['message']);
        } else {
            return $this->error($result['message']);
        }
    }
    public function getUser ()
    {
        $user =$this->AuthService->getUser() ;
        return $user;
    }

}
