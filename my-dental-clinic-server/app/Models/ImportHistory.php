<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{
    use HasFactory;

    protected $fillable = ['import_date', 'importer', 'note'];

    // Quan hệ 1-n với MedicineBatch
    public function medicineBatches()
    {
        return $this->hasMany(MedicineBatch::class, 'import_history_id');
    }
}

