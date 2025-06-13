<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppointmentService;
class AppointmentController extends ApiResponseController
{
    protected $AppointmentService ;
    public function __construct(AppointmentService $AppointmentService )
    {
        $this->AppointmentService = $AppointmentService;
    }
    public function getAppointments(Request $request)
    {
        $keyword=$request->input('keyword');
        $status=$request->input('status');
        $perPage = $request->input('per_page', 5); 
        $appointments = $this->AppointmentService->getAppointments($perPage, $keyword, $status);

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

   
    public function updateAppointmentDetails(Request $request, $id)
    {
        $data = $request->validate([
            'estimated_duration' => 'required|integer|min:15',
            'notes' => 'nullable|string'
        ]);

        $result = $this->AppointmentService->updateAppointmentDetails($id, $data);
        if ($result['success']) {
            return $this->success($result['message']);
        }
        return $this->error($result['message']);
    }
}
