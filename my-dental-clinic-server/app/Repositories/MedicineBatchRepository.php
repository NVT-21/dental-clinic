<?php

namespace App\Repositories;
use App\Models\MedicineBatch;
 class MedicineBatchRepository extends BaseRepository
 {
    function getModel(){
        return MedicineBatch::class ;
    }
    public function getMedicineBatch($pageSize, $keyword = null)
    {
        
        $query = MedicineBatch::with('medicine');
        if ($keyword) {
            $query->whereHas('medicine', function ($q) use ($keyword) {
                $q->where('medicine_name', 'like', "%$keyword%");
            });
        }
        return $query->paginate($pageSize);
    }
    
 }