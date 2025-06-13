<?php

namespace App\Repositories;
use App\Models\User;
use App\Models\Employee;
 class AuthRepository extends BaseRepository
 {
    function getModel(){
        return User::class ;
    }
    public function findByEmail(string $email)
    {
        return User::with('employee')->where('email', $email)->first();
    }
    public function updateEmployee($id, $data)
    {
        $employee = Employee::findOrFail($id);
        $employee->update($data);
        return $employee;
    }
 }