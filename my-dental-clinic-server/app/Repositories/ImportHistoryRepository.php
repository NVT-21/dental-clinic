<?php

namespace App\Repositories;
use App\Models\ImportHistory;
 class ImportHistoryRepository extends BaseRepository
 {
    function getModel(){
        return ImportHistory::class ;
    }
    
 }