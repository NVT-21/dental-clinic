<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkScheduleDetail extends Model
{
    protected $fillable = ['workScheduleId', 'shiftId', 'status'];


    public function workSchedule() {
        return $this->belongsTo(WorkSchedule::class, 'workScheduleId');
    }

    
    public function workShift() {
        return $this->belongsTo(WorkShift::class, 'shiftId');
    }
}
