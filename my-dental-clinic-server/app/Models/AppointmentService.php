<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class AppointmentService extends Model
{
    use HasFactory;
    protected $table = 'appointment_services';
    
    protected $fillable = [
        'appointment_id',
        'service_id',
        'notes' // Ghi chú riêng cho dịch vụ này
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
} 