<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class AppointmentLog extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'appointment_id',
        'status',
        'employee_id',
        'created_at',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
