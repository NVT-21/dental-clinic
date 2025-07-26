<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Employee extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'fullName', 'birthday','idRoom', 'role','gender', 'phoneNumber','status'];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class, 'idEmployee');
    }
    public function medicalExams()
    {
        return $this->hasMany(MedicalExam::class, 'idEmployee'); 
    }
    public function room()
    {
        return $this->belongsTo(Room::class, 'idRoom');
    }
    public function appointmentLogs()
{
    return $this->hasMany(AppointmentLog::class);
}


}
