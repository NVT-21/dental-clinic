<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
class ApiResponseController extends Controller
{
    /**
     * Trả về phản hồi thành công
     *
     * @param string $message
     * @param array|null $data
     * @param int $statusCode
     * @return JsonResponse
     */
    public function success($message = 'Success', $data = null, $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Trả về phản hồi lỗi
     *
     * @param string $message
     * @param array|null $data
     * @param int $statusCode
     * @return JsonResponse
     */
    public function error($message = 'Error', $data = null, $statusCode = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
    public function getEmployee()
    {
        $user = Auth::user();
        return $user ? Employee::where('user_id', $user->id)->first() : null;
    }
    public function getUser()
    {
        return  User::with('roles')->find(Auth::id());
    }
    public function roleNameById($id)
    {
        return Role::where("id", $id)->first()?->name; 
    }
    public function getDoctorsWithoutRoom()
    {
        return Employee::whereNull('idRoom')
            ->where('role', 'Doctor')
            ->where('status', 'working') // thêm điều kiện status
            ->get();
    }
    
    

    
}
