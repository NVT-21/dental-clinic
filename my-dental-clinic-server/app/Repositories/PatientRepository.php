<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\Patient;
 class PatientRepository extends BaseRepository
 {
    function getModel(){
        return Patient::class ;
    }
    public function getByPhoneNumber($phoneNumber)
    {
        return Patient::with('Appointment')->where("phoneNumber",$phoneNumber)->first();
    }
   public function searchPatientByPhone($conditions)
{
    $phoneNumber = $conditions['phoneNumber'] ?? '';
    $fullname = $conditions['fullname'] ?? '';
    $birthdate = $conditions['birthdate'] ?? '';

    if (empty($phoneNumber) && empty($fullname) && empty($birthdate)) {
        return [
            'success' => false,
            'message' => 'At least one of phone number, full name, or birthdate is required',
            'status' => 400, // Include status code for the controller to use
        ];
    }

    $query = Patient::query();

    if (!empty($phoneNumber)) {
        $query->where('phoneNumber', $phoneNumber);
    }
    if (!empty($fullname)) {
        $query->where('fullname', 'like', '%' . $fullname . '%');
    }
    if (!empty($birthdate)) {
        $query->whereDate('birthdate', $birthdate);
    }

    $patients = $query->with('latestAppointment')->get();

    return [
        'success' => true,
        'message' => 'Patients retrieved successfully',
        'data' => $patients,
        'status' => 200, // Include status code for success
    ];
}
   public function findByPhoneAndName($name,$phoneNumber)
    {
        $patient = Patient::where('phoneNumber', $phoneNumber)
        ->where('fullname', $name)
        ->first();
        return $patient;
    }
      public function findByPhone($phoneNumber)
    {
        $patient = Patient::where('phoneNumber', $phoneNumber)->get();
        return $patient;
    }
    public function getMedicalExamsOfPatient($patientId)
    {
        return Patient::with(['appointments.medicalExam' => function($query) {
            // Lọc theo status của MedicalExam
            $query->where('medical_exams.status', 'Completed');
        }])
        ->where('id', $patientId)
        ->first();  
    }
    

    

 }