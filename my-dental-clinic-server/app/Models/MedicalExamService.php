<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class MedicalExamService extends Model
{
    use HasFactory;
    protected $fillable = [
        'medical_exam_id',
        'service_id',
        'quantity',
        'content',
        'price',
    ];

    // Quan hệ với bảng MedicalExam
    public function medicalExam()
    {
        return $this->belongsTo(MedicalExam::class, 'medical_exam_id');
    }

    // Quan hệ với bảng Service
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
