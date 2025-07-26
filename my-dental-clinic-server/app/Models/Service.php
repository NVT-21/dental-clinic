<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'serviceName', 
        'description', 
        'base_price',
        
    ];

    public function medicalExams() {
        return $this->belongsToMany(MedicalExam::class, 'medical_exam_services')
                    ->withPivot('quantity', 'content', 'price')
                    ->withTimestamps();
    }
}
