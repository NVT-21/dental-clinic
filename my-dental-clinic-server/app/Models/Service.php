<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'serviceName', 
        'description', 
        'base_price',
        'estimated_time' // Thời gian ước lượng mặc định (phút)
    ];

    public function medicalExams() {
        return $this->belongsToMany(MedicalExam::class, 'medical_exam_services')
                    ->withPivot('quantity', 'content', 'price')
                    ->withTimestamps();
    }
}
