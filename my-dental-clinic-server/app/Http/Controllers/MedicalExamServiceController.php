<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MedicalExamServiceService;
class MedicalExamServiceController extends ApiResponseController
{
    protected $MedicalExamServiceService ;
    public function __construct(MedicalExamServiceService $MedicalExamServiceService )
    {
        $this->MedicalExamServiceService = $MedicalExamServiceService;
    }
   public function store(Request $request,$idMedicalExam)
   {
    $services=$request->all();
    return $this->MedicalExamServiceService->store($services,$idMedicalExam);
   }
   public function getServiceByIdMedicalExam($idMedicalExam)
   {
    return $this->MedicalExamServiceService->getServiceByIdMedicalExam($idMedicalExam);
   }
   public function deleteService (Request $request)
   {
    $data=$request->all();
    return $this->MedicalExamServiceService->deleteService($data);
   }
}
