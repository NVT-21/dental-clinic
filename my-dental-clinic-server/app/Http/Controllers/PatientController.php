<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PatientService;
class PatientController extends ApiResponseController
{
    protected $PatientService ;
    public function __construct(PatientService $PatientService )
    {
        $this->PatientService = $PatientService;
    }
    public function index()
    {
        return view('Customer.home');
    }


    public function store(Request $request)
    {
     $data = $request->all();
     $result= $this->PatientService->createAppointment($data);
     $a=$result->getData();
      return $result->getData();
    }


    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function patient()
    {
        return view ('Admin.list-patient');
    }
    public function findByNumberPhone(Request $request)
    {
        $conditions=$request->all();
        $result= $this->PatientService->searchPatientByPhone($conditions);
        if($result['success'])
        {
            return $this->success($result['message'],$result['data']);
        }
        else {
            return $this->error($result['message']);
        }
    }
    public function getMedicalExamsOfPatient(Request $request)
    {
       $patientId=$request->input('patientId');
       return $this->PatientService->getMedicalExamsOfPatient($patientId);
    }
   public function paging(Request $request) 
{
    $input = $request->all();
    return $this->PatientService->paging($input);
}
public function createOrUpdate(Request $request)
{
    $input=$request->all();
    return $this->PatientService->createOrUpdate($input);
}

}
