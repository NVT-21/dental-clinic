<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Patient extends Model
{
    use HasFactory;
    protected $fillable = [
        'fullname',
        'phoneNumber',
        'email',
        'birthdate',
        'message',
    ];
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'idPatient');
    }
    public function latestAppointment()
    {
        return $this->hasOne(Appointment::class, 'idPatient')
            ->whereDate('bookingDate', '>=', now()->toDateString())
            ->where('status', 'Confirmed')
             ->where('is_done', false)
            ->latest('bookingDate');
    }

}
