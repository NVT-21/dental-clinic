<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentService extends Model
{
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