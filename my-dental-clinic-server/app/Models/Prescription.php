<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = ['idMedicalExam'];

    // Quan hệ nhiều - nhiều với Medicines
    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'medicine_prescription')
                    ->withPivot('quantity','total_price')
                    ->withTimestamps();
    }
    public function medicalExam()
{
    return $this->belongsTo(MedicalExam::class, 'idMedicalExam');
}

}

