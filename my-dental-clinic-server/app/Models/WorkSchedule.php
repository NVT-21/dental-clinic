<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class WorkSchedule extends Model
{
    use HasFactory;
    protected $fillable = ['registerDate', 'idEmployee'];

  
    public function employee() {
        return $this->belongsTo(Employee::class, 'idEmployee');
    }

        public function workScheduleDetails() {
        return $this->hasMany(WorkScheduleDetail::class, 'workScheduleId');
    }
}
