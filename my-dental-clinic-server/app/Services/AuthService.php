<?php 
namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class AuthService extends BaseService
{
    protected $AuthRepository;

    public function __construct(AuthRepository $AuthRepository)
    {
        $this->AuthRepository = $AuthRepository;
        parent::__construct();
    }
    public function getRepository()
    {
        return $this->AuthRepository;
    }
    public function login($credentials)
    {
        $user = $this->AuthRepository->findByEmail($credentials['email']);
        
        // Check if user exists
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }
    
        // Check status for non-admin users
        $isAdmin = $user->roles->contains('name', 'admin');
        if (!$isAdmin && $user->employee && $user->employee->status === 'resigned') {
            return [
                'success' => false,
                'message' => 'Account is no longer active'
            ];
        }
    
        // Attempt authentication
        if (!Auth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }
    
        $token = $user->createToken('authToken')->plainTextToken;
        
        return [
            'success' => true,
            'message' => "Login Successfully!",
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'roles' => $user->roles->pluck('name'),
               'employee' => $user->employee ? $user->employee->toArray() : null,
            ],
            'token' => $token,
        ];
    }
    
    public function assignRole ($user,$role)
    {
        $newUser=$this->AuthRepository->create($user);
        $newUser->roles()->attach($role);
        return $newUser;
    }
    public function isEmail($email)
    {
        if($this->AuthRepository->findByEmail($email))
        {
            return true;
        }
        else {
            return false;
        }
    }
    public function changePassword($data)
    {    $user = Auth::user();
        if (!$user instanceof User) {
            return ['success' => false, 'message' => 'User not found or not authenticated'];
        }
        if (!Hash::check($data['currentPassword'], $user->password))
        {
            return ['success' => false,'message' => 'Current password is incorrect'];
        }
        $user->password = Hash::make($data['newPassword']);
        $user->save();
        return ['success' => true,'message' => 'Password changed successfully'];
    }
    public function getUser()
    {
        $user= Auth::user();
        if(!$user)
        {
            return ['success' => false,'message' => 'User not found or not authenticated'];
        }
        return ['success' => true, 'user' => $user];
    }
    public function updateEmployee($id, $data)
    {
       return $this->AuthRepository->updateEmployee($id,$data);
    }
}