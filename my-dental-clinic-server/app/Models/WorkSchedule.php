<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $fillable = ['registerDate', 'idEmployee'];

  
    public function employee() {
        return $this->belongsTo(Employee::class, 'idEmployee');
    }

        public function workScheduleDetails() {
        return $this->hasMany(WorkScheduleDetail::class, 'workScheduleId');
    }
}
