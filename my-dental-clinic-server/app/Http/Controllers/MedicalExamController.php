<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MedicalExamService;
class MedicalExamController extends ApiResponseController
{
    protected $MedicalExamService ;
    public function __construct(MedicalExamService $MedicalExamService )
    {
        $this->MedicalExamService = $MedicalExamService;
    }
    public function store(Request $request)
    {
        $creator=$this->getEmployee();
        $data=$request->all();
        $result=$this->MedicalExamService->createMedicalExam($data,$creator);
        if ($result['success']) {
            return $this->success($result['message']);
         } else {
         
             return $this->error($result['message']);
         }
    }
    public function getMedicalExam(Request $request)
        {   $status = $request->input('status', null); 
        $perPage = $request->input('perPage', 10);
        $paymentStatus = $request->input('paymentStatus', null);
        $idEmployee=null;
        $user = $this->getUser();
        if (
           $user &&
           (in_array('Doctor', $user->roles->pluck('name')->toArray()) )
       ) {
           $employee = $user->employee;
           if ($employee) {
               $idEmployee = $employee->id;
           }
       }
        return $this->MedicalExamService->getMedicalExam($perPage,$status,$paymentStatus,$idEmployee);
    }
    public function saveDoctorConclusion(Request $request)
    {
        $data=$request->all();
        return $this->MedicalExamService->saveDoctorConclusion($data);
    }
 public function update(Request $request, $idMedicalExam)
{
    $data = $request->only([
        'symptoms',
        'status',
        'statusPayment',
        'examDate',
        'diagnosis',
        'advice'
    ]);

    $updated = $this->MedicalExamService->updateMedicalExam($idMedicalExam, $data);

    return response()->json([
        'success' => $updated['success'],
        'message' => $updated['message'],
        'data' => $updated['data']
    ]);
}
    public function getMedicalExamById($idMedicalExam)
    {
        return $this->MedicalExamService->getById($idMedicalExam);
    }
    public function getPrescriptionAndService($id)
    {
        return $this->MedicalExamService->getPrescriptionAndService($id);
    }
}
