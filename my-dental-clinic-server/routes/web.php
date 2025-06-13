<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\WorkScheduleController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MedicalExamController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\MedicineBatchController;
use App\Http\Controllers\MedicalExamServiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StatisticsController;
use App\Models\Appointment;
use App\Models\Medicine;
use App\Models\Room;
use App\Models\Service;

//Patient
Route::get("/",[PatientController::class,'index'])->name("patient.home");
Route::get("/patient",[PatientController::class,'patient'])->name("patient");
Route::get("/login",[PatientController::class,'signIn'])->name("signIn");




//Auth
Route::get("/login",[AuthController::class,'showLoginForm'])->name("Auth.viewLogin");
Route::prefix('api')->group(function () {
    //Patient
    Route::get("/getMedicalExamsOfPatient",[PatientController::class,'getMedicalExamsOfPatient']);
    Route::post("/createAppointment",[PatientController::class,'store']);
    Route::get("/getPatients",[PatientController::class,'paging']);
    Route::post("/createOrUpdatePatient",[PatientController::class,'createOrUpdate']);
    //AUTH
    Route::post("/saveOrUpdateEmployee", [AuthController::class, 'saveOrUpdateEmployee'])->name("Auth.register");
    Route::post("/login",[AuthController::class,'login'])->name("Auth.login");
    // APPOINTMENT
    Route::get("/getAppointments",[AppointmentController::class,'getAppointments'])->name("Appointment.getAppointments");
     Route::get("/getConfirmedAppointmentsInTimeRange",[AppointmentController::class,'getConfirmedAppointmentsInTimeRange']);
    //Employee
    Route::get("/doctor-without-room",[EmployeeController::class,'getDoctorWithoutRoom']);
    Route::get("/getDoctorWorking",[WorkScheduleController::class,'getDoctorWorking']);
    Route::get("/employees",[EmployeeController::class,'paging']);
    //Room
    Route::get("/doctor-in-room/{id}",[RoomController::class,'getDoctorInRoom']);
    //Medical-exam
    Route::get("/patientByPhone",[PatientController::class,'findByNumberPhone']);
    Route::get("/roomOfDoctor/{id}",[EmployeeController::class,'getRoomOfDoctor']);
    Route::post("/saveDoctorConclusion",[MedicalExamController::class,'saveDoctorConclusion']);
    Route::put("/medicalExam/{idMedicalExam}",[MedicalExamController::class,'update']);
    Route::get("/medicalExamById/{id}",[MedicalExamController::class,'getMedicalExamById']);
    Route::get("/PrescriptionAndService/{id}",[MedicalExamController::class,'getPrescriptionAndService']);
    // Service
    Route::post("/createOrUpdateService",[ServiceController::class,'createOrUpdate']);
    Route::get("/services",[ServiceController::class,'paging']);
    Route::get("/services/getByName",[ServiceController::class,'getByName']);
    Route::delete("/service/{id}",[ServiceController::class,'delete']);
    //Medicine
    Route::post("/medicine",[MedicineController::class,'store']);
    Route::get("/medicineByName",[MedicineController::class,'findByName']);
    Route::get("/validMedicine",[MedicineController::class,'getValidMedincineQuantity']);
    Route::post("/prescribeMedicine/{idMedicalExam}",[MedicineController::class,'prescribeMedicine']);
    Route::get("/getMedicinesByMedicalExam/{idMedicalExam}",[MedicineController::class,'getMedicinesByMedicalExam']);
    //Medicine Batch 
    Route::get("/medicineBatchs",[MedicineBatchController::class,'getMedicineBatchs']);
    //MedicalExamService
    Route::post("/medicalExamService/{idMedicalExam}",[MedicalExamServiceController::class,'store']);
    Route::get("/getServiceByMedicalExam/{idMedicalExam}",[MedicalExamServiceController::class,'getServiceByIdMedicalExam']);
    Route::delete("/deleteService",[MedicalExamServiceController::class,'deleteService']);
    Route::middleware('auth:sanctum')->group(function () {
            //stats
            Route::get('/statistics', [StatisticsController::class, 'getStatistics']);
        //Notifition
        Route::get("/notifications/unread", [NotificationController::class, 'getUnreadNotifications']);
        Route::post('/notifications/mark-as-read', [NotificationController::class, 'markNotificationsAsRead']);
        //
        Route::post("/changePassword", [AuthController::class, 'changePassword'])->name("Auth.changePassword");
        Route::get("/getUser",[AuthController::class,'getUser'])->name("Auth.getUser");
        Route::post("/createWorkSchedule",[WorkScheduleController::class,'createOrUpdate'])->name("WorkSchedule.createOrUpdate");
        //Room
        Route::get("/rooms",[RoomController::class,'paging'])->name("Room.paging");
        Route::post("/createRoom",[RoomController::class,'store'])->name("Room.store");
        Route::post("/add-doctors-room",[RoomController::class,'updateDoctorsToRoom'])->name("Room.addDoctorsToRoom");
            //Medicine Batch
        Route::post("/medicine-batch",[MedicineBatchController::class,'createMedicineBatch']);
        //work Schedule 
        Route::get("/work-schedule/paging",[WorkScheduleController::class,'paging'])->name("WorkSchedule.paging");
        //MedicalExam
        Route::get("/medicalExams",[MedicalExamController::class,'getMedicalExam']);
         Route::post("/createMedicalExam",[MedicalExamController::class,'store']);
        //appointment
        Route::patch("/getAppointments/{id}",[AppointmentController::class,'update'])->name("Appointment.update");
        Route::post('/broadcasting/auth', [\Illuminate\Broadcasting\BroadcastController::class, 'authenticate']);
      
    });
});
