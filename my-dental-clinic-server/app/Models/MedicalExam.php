<?php

namespace App\Models;

use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Model;

class MedicalExam extends Model
{
    protected $fillable = ['idEmployee', 'idAppointment', 'symptoms', 'status','statusPayment' 
    ,'statusPayment', 'ExamDate','diagnosis','advice','createdById'];

  
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'idEmployee');
    }
    public function receptionist()
    {
        return $this->belongsTo(Employee::class, 'idRep');
    }
  
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'idAppointment');
    }
    public function services() {
        return $this->belongsToMany(Service::class, 'medical_exam_services')
                    ->withPivot('quantity', 'content', 'price')
                    ->withTimestamps();
    }
   public function creator()
    {
        return $this->belongsTo(Employee::class, 'createdById'); // Liên kết với người tạo
    }
    public function prescription()
    {
        return $this->hasOne(Prescription::class, 'idMedicalExam');
    }
    
    

    
}
