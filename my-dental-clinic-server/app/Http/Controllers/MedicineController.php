<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MedicineService;
class MedicineController extends ApiResponseController
{
    protected $MedicineService ;
    public function __construct(MedicineService $MedicineService )
    {
        $this->MedicineService = $MedicineService;
    }
    public function store(Request $request)
    {
        $data=$request->only('medicine_name', 'unit', 'instructs');
        $result=$this->MedicineService->create($data);
        if ($result['success']) {
            return $this->success($result['message'],$result['data']);
         } else {
         
             return $this->error($result['message']);
         }
    }
    public function findByName(Request $request)
    {
        $name = $request->input("name");
        return $this->MedicineService->findByName($name);
    }
    public function getValidMedincineQuantity(Request $request)
    {
        return $this->MedicineService->getValidMedincineQuantity(1);
    }
    public function prescribeMedicine(Request $request ,$idMedicalExam)
    {
        $data=$request->input('medicines');
        return $this->MedicineService->prescribeMedicine($idMedicalExam,$data);
    }
    public function getMedicinesByMedicalExam($idMedicalExam)
    {
        return $this->MedicineService->getMedicinesByMedicalExam($idMedicalExam);
    }
}
