<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class WorkScheduleDetail extends Model
{
    use HasFactory;
    protected $fillable = ['workScheduleId', 'shiftId', 'status'];


    public function workSchedule() {
        return $this->belongsTo(WorkSchedule::class, 'workScheduleId');
    }

    
    public function workShift() {
        return $this->belongsTo(WorkShift::class, 'shiftId');
    }
}
