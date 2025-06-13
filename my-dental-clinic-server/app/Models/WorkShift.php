<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    protected $fillable = ['shiftName', 'startTime', 'endTime']; 

    // Một ca làm việc có thể xuất hiện trong nhiều lịch làm việc chi tiết
    public function workScheduleDetails() {
        return $this->hasMany(WorkScheduleDetail::class, 'shiftId');
    }
}
