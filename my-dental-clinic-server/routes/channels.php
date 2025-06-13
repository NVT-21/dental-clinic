<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('doctor.{employeeId}', function ($user, $employeeId) {
    Log::info('Broadcast Auth ', ['user' => $user, 'employeeId' => $employeeId]);

    // Check if $user is authenticated
    if (!$user) {
        Log::warning('User not authenticated', ['user' => $user]);
        return false;
    }

    // Check if $user->employee exists and is a valid Employee instance
    if (!$user->employee || !($user->employee instanceof \App\Models\Employee)) {
        Log::warning('Employee relation not found or invalid', ['user' => $user]);
        return false;
    }

    // Compare employee ID with the requested employeeId
    $isAuthorized = $user->employee->id === (int)$employeeId;
    Log::info('Authorization result', ['isAuthorized' => $isAuthorized, 'employeeId' => $employeeId]);

    return $isAuthorized;
});
Broadcast::channel('manager.{managerId}', function ($user, $managerId) {
    Log::info('Broadcast Auth for manager channel', [
        'user' => $user,
        'managerId' => $managerId,
        'userEmployee' => $user->employee ?? null
    ]);

    // Check if user is authenticated
    if (!$user) {
        Log::warning('User not authenticated');
        return false;
    }

    // Check if user has employee relation
    if (!$user->employee) {
        Log::warning('User has no employee relation');
        return false;
    }

    // Check if user is a manager
    $isManager = $user->employee->role === 'Manager';
    Log::info('Manager check', [
        'userRole' => $user->employee->role,
        'isManager' => $isManager
    ]);

    if (!$isManager) {
        Log::warning('User is not a manager');
        return false;
    }

    // Check if manager ID matches
    $isAuthorized = $user->employee->id === (int)$managerId;
    Log::info('Manager authorization result', [
        'isAuthorized' => $isAuthorized,
        'managerId' => $managerId,
        'userEmployeeId' => $user->employee->id
    ]);

    return $isAuthorized;
});
Broadcast::channel('employee.{employeeId}', function ($user, $employeeId) {
    Log::info('Broadcast Auth for employee channel', [
        'user' => $user,
        'employeeId' => $employeeId,
        'userEmployee' => $user->employee ?? null
    ]);

    // Check if user is authenticated
    if (!$user) {
        Log::warning('User not authenticated');
        return false;
    }

    // Check if user has employee relation
    if (!$user->employee) {
        Log::warning('User has no employee relation');
        return false;
    }

    // Check if employee ID matches
    $isAuthorized = $user->employee->id === (int)$employeeId;
    Log::info('Employee authorization result', [
        'isAuthorized' => $isAuthorized,
        'employeeId' => $employeeId,
        'userEmployeeId' => $user->employee->id
    ]);

    return $isAuthorized;
});
