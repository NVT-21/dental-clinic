<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'idPatient', 
        'bookingDate', 
        'appointmentTime', 
        'status',
        'is_done',
        'symptoms', // Triệu chứng/tình trạng bệnh
        'medical_history', // Tiền sử bệnh
        'notes',
        'appointment_type', 
        'estimated_duration',// Tổng thời gian dự kiến của cuộc hẹn
        'locked_by' ,
        'locked_at'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'idPatient');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'appointment_services')
                    ->withPivot('estimated_duration', 'notes')
                    ->withTimestamps();
    }

    public function appointmentServices()
    {
        return $this->hasMany(AppointmentService::class);
    }

    public function medicalExam()
    {
        return $this->hasOne(MedicalExam::class, 'idAppointment');
    }

    public function logs()
    {
        return $this->hasMany(AppointmentLog::class)->with('employee');
    }

    // Tính tổng thời gian ước lượng của tất cả dịch vụ
    public function getTotalEstimatedDuration()
    {
        return $this->appointmentServices()->sum('estimated_duration');
    }
    // Appointment.php
    public function lockedBy()
    {
        return $this->belongsTo(Employee::class, 'locked_by');
    }

}
