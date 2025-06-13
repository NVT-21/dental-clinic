<?php

namespace App\Repositories;
use App\Models\MedicalExamService;
use App\Models\Appointment;
use App\Models\MedicalExam;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
 class MedicalExamServiceRepository extends BaseRepository
 {
    function getModel(){
        return MedicalExamService::class ;
    }
    public function store($data,$idMedicalExam)
    {
        $medicalExam=MedicalExam::where('id',$idMedicalExam);
        if(!$medicalExam)
        {
            return null ;
        }
        foreach ($data as $service) {
            $existing = MedicalExamService::where('medical_exam_id', $idMedicalExam)
                                          ->where('service_id', $service['key'])
                                          ->first();
    
            if ($existing) {
                $existing->update([
                    'quantity' => $service['quantity'] ?? $existing->quantity,
                    'content' => $service['content'] ?? $existing->content,
                    'price' => $service['price'] ?? $existing->price,
                ]);
            } else {
                MedicalExamService::create([
                    'medical_exam_id' => $idMedicalExam,
                    'service_id' => $service['key'],
                    'quantity' => $service['quantity'] ?? 1,
                    'content' => $service['content'] ?? null,
                    'price' => $service['price'],
                ]);
            }
        }
    }
    public function getServiceByIdMedicalExam($id)
    {
    return MedicalExamService::with('Service')
        ->where('medical_exam_id', $id)
        ->get();
    }
    public function deleteService($data)
    {
        $idService =$data['Service'];
        $idMedicalExam=$data['MedicalExam'];
        return MedicalExamService::where('service_id', $idService)
        ->where('medical_exam_id', $idMedicalExam)
        ->delete();
    }


   
   
 }