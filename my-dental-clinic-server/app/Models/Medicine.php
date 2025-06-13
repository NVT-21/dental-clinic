<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Medicine extends Model
{
    use HasFactory;

    protected $fillable = ["id",'medicine_name', 'unit', 'instructs'];

    // Quan hệ nhiều - nhiều với Prescriptions
    public function prescriptions()
    {
        return $this->belongsToMany(Prescription::class, 'medicine_prescription')
                    ->withPivot('quantity','total_price') // Lấy cột quantity từ bảng trung gian
                    ->withTimestamps();
    }
    public function medicineBatches()
    {
        return $this->hasMany(MedicineBatch::class, 'medicine_id','id');
    }
}
