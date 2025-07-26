<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class WorkShift extends Model
{
    use HasFactory;
    protected $fillable = ['shiftName', 'startTime', 'endTime']; 

    // Một ca làm việc có thể xuất hiện trong nhiều lịch làm việc chi tiết
    public function workScheduleDetails() {
        return $this->hasMany(WorkScheduleDetail::class, 'shiftId');
    }
}
