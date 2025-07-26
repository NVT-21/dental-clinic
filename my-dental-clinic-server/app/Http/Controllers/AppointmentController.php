<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppointmentService;
use App\Models\Appointment;
class AppointmentController extends ApiResponseController
{
    protected $AppointmentService ;
    public function __construct(AppointmentService $AppointmentService )
    {
        $this->AppointmentService = $AppointmentService;
    }
    public function getAppointments(Request $request)
    {
        $user = $this->getUser();
        $role = $user->roles->first()->name ?? null;
        $keyword = $request->input('keyword');
        $status = $request->input('status');
        $date = $request->input('date');
        $perPage = $request->input('per_page', 5);
        $appointments = $this->AppointmentService->getAppointments($perPage, $keyword, $status, $role, $date);
    
        return response()->json($appointments);
    }
    public function update (Request $request,$id)
    {
        $employee=$this->getEmployee();
        $data=$request->all();
        $result=$this->AppointmentService->updateAppointment($id,$data,$employee);
        if ($result['success']) {
            return $this->success($result['message']);
         } else {
         
             return $this->error($result['message']);
         }
    }
       public function getConfirmedAppointmentsInTimeRange(Request $request )
    {
        $data=$request->only('time','date','durationValue');
        return $this->AppointmentService->getConfirmedAppointmentsInTimeRange($data);
    }

    public function checkAndSuggestSlotWithSubBlock(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'startTime' => 'required|date_format:H:i',
            'totalEstimatedDuration' => 'required|integer|min:3|max:240',
            'maxSuggestions' => 'sometimes|integer|min:1|max:10'
        ]);

        $date = $data['date'];
        $startTime = $data['startTime'];
        $totalEstimatedDuration = $data['totalEstimatedDuration'];
        $maxSuggestions = $data['maxSuggestions'] ?? 30; // Mặc định 5 nếu không có

        // Gọi repository để kiểm tra
        $result = $this->AppointmentService->checkAndSuggestSlotWithSubBlock(
            $date,
            $startTime,
            $totalEstimatedDuration,
            $maxSuggestions
        );

        return response()->json($result);
    }
    // AppointmentController.php
    public function lock($id)
    {
        $employee = $this->getEmployee();
        if (!$employee) {
            return response()->json(['message' => 'Unable to identify employee'], 403);
        }
    
        $appointment = Appointment::findOrFail($id);
    
        if ($appointment->locked_by && $appointment->locked_by !== $employee->id) {
            // Get locker's name
            $locker = $appointment->lockedBy; // relation
            $lockerName = $locker?->fullName ?? 'Someone';
            return response()->json([
                'locked_by' => $appointment->locked_by,
                'locked_by_name' => $lockerName,
                'locked_at' => $appointment->locked_at,
            ], 409);
        }
    
        // Update lock
        $appointment->update([
            'locked_by' => $employee->id,
            'locked_at' => now(),
        ]);
    
        return response()->json(['message' => 'Appointment locked successfully']);
    }
    

public function unlock($id)
{
    $employee = $this->getEmployee();
    if (!$employee) {
        return response()->json(['message' => 'Unable to identify employee'], 403);
    }

    $appointment = Appointment::findOrFail($id);

    // Only the locker can unlock
    if ($appointment->locked_by === $employee->id) {
        $appointment->update([
            'locked_by' => null,
            'locked_at' => null,
        ]);
    }

    return response()->json(['message' => 'Appointment unlocked successfully']);
}
}
