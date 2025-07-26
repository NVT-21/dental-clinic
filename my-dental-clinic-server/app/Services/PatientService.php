<?php 
namespace App\Services;

use App\Repositories\PatientRepository;
use App\Repositories\AppointmentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Appointment;
use Throwable;
use Illuminate\Support\Facades\Hash;

class PatientService extends BaseService
{
    protected $PatientRepository;
    protected $appointmentRepository;
    public function __construct(PatientRepository $patientRepository,AppointmentRepository $appointmentRepository)
    {
        $this->PatientRepository = $patientRepository;
        $this->appointmentRepository = $appointmentRepository;
        parent::__construct();
    }
    public function getRepository()
    {
        return $this->PatientRepository;
    }
    public function createAppointment($data)
    {
        DB::beginTransaction();
        try {
            $fullname = $data['fullname'];
            $phoneNumber = $data['phoneNumber'];
            $birthdate = $data['birthdate'];
            $email = $data['email'] ?? null;
            $appointmentDate = $data['appointmentDate'];
            $appointmentTime = $data['appointmentTime'];
            $forceNewPatient = $data['forceNewPatient'] ?? false;
            $services = $data['services'] ?? [];
            $symptoms = $data['symptoms'] ?? null;
            $appointmentType = $data['appointment_type'] ?? 'consultation';
            $medicalHistory = $data['medical_history'] ?? null;

            // Find patient by phone number
            $patients = $this->PatientRepository->findByPhone($phoneNumber);
            if ($patients->isNotEmpty()) {
                // Check if there's a patient with a matching name
                $matchedPatient = $patients->firstWhere('fullname', $fullname);

                if ($matchedPatient) {
                    // Patient matches: Create a new appointment
                    $patient = $matchedPatient;

                    // Check for duplicate appointments
                    $existingAppointment = Appointment::where('idPatient', $patient->id)
                        ->where('bookingDate', $appointmentDate)
                        ->where('appointmentTime', $appointmentTime)
                        ->where('status', '!=', 'Cancelled')
                        ->exists();

                    if ($existingAppointment) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Patient already has an appointment at this time',
                        ], 400);
                    }

                    $appointment = $this->appointmentRepository->create([
                        'idPatient' => $patient->id,
                        'bookingDate' => $appointmentDate,
                        'appointmentTime' => $appointmentTime,
                        'status' => 'Waiting for Estimation',
                        'is_done' => false,
                        'appointment_type' => $appointmentType,
                        'symptoms' => $symptoms,
                        'medical_history' => $medicalHistory,
                    ]);

                    // Attach services to appointment
                    if (!empty($services)) {
                        foreach ($services as $serviceId) {
                            $appointment->appointmentServices()->create([
                                'service_id' => $serviceId,
                                'estimated_duration' => null, // Will be set by receptionist
                                'notes' => null
                            ]);
                        }
                    }

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Appointment created for existing patient',
                        'data' => $appointment->load('appointmentServices.service'),
                    ], 200);
                } elseif ($forceNewPatient) {
                    // Force creation of a new patient with the same phone number
                    $patient = $this->PatientRepository->create([
                        'fullname' => $fullname,
                        'phoneNumber' => $phoneNumber,
                        'birthdate' => $birthdate,
                        'email' => $email,
                    ]);

                    $appointment = $this->appointmentRepository->create([
                        'idPatient' => $patient->id,
                        'bookingDate' => $appointmentDate,
                        'appointmentTime' => $appointmentTime,
                        'status' => 'Waiting for Estimation',
                        'is_done' => false,
                        'appointment_type' => $appointmentType,
                        'symptoms' => $symptoms,
                        'medical_history' => $medicalHistory,
                    ]);

                    // Attach services to appointment
                    if (!empty($services)) {
                        foreach ($services as $serviceId) {
                            $appointment->appointmentServices()->create([
                                'service_id' => $serviceId,
                                'estimated_duration' => null,
                                'notes' => null
                            ]);
                        }
                    }

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'New patient and appointment created successfully',
                        'data' => $appointment->load('appointmentServices.service'),
                    ], 201);
                } else {
                    // Multiple patients with the same phone number, but names don't match
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Found patients with this phone number but names do not match. Please select or create a new patient.',
                        'data' => $patients,
                        'allowNewPatient' => true,
                    ], 409);
                }
            }

            // No patient found, create a new one
            $patient = $this->PatientRepository->create([
                'fullname' => $fullname,
                'phoneNumber' => $phoneNumber,
                'birthdate' => $birthdate,
                'email' => $email,
            ]);

            // Create appointment for the new patient
            $appointment = $this->appointmentRepository->create([
                'idPatient' => $patient->id,
                'bookingDate' => $appointmentDate,
                'appointmentTime' => $appointmentTime,
                'status' => 'Pending',
                'is_done' => false,
                'appointment_type' => $appointmentType,
                'symptoms' => $symptoms,
                'medical_history' => $medicalHistory,
            ]);

            // Attach services to appointment
            if (!empty($services)) {
                foreach ($services as $serviceId) {
                    $appointment->appointmentServices()->create([
                        'service_id' => $serviceId,
                        'estimated_duration' => null,
                        'notes' => null
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully for new patient',
                'data' => $appointment->load('appointmentServices.service'),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Failed to create appointment: ' . $e->getMessage());
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function searchPatientByPhone($conditions)
    {
       return $this->PatientRepository->searchPatientByPhone($conditions);
      
    }
    public function getMedicalExamsOfPatient($patientId)
    {
        return $this->PatientRepository->getMedicalExamsOfPatient($patientId);
    }
    
}