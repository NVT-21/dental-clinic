<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['idEmployee', 'message', 'read_at'];
    public $timestamps = false; // Tắt timestamp
}
