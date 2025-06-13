<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineBatch extends Model
{
    use HasFactory;

    protected $fillable = ['import_history_id', 'medicine_id', 'expiration_date', 'cost_price', 'selling_price', 'initial_quantity', 'remaining_quantity'];

    // Quan hệ n-1 với ImportHistory
    public function importHistory()
    {
        return $this->belongsTo(ImportHistory::class, 'import_history_id');
    }

    // Quan hệ n-1 với Medicine
    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id');
    }
}

