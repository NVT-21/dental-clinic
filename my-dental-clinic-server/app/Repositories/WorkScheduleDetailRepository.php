<?php

namespace App\Repositories;
use App\Models\WorkScheduleDetail;
 class WorkScheduleDetailRepository extends BaseRepository
 {
    function getModel(){
        return WorkScheduleDetail::class ;
    }
    
    
    function updateWorkScheduleDetail($workScheduleId,$shiftId,$status)
    {
        return WorkScheduleDetail::updateOrCreate(
            ['workScheduleId' => $workScheduleId, 'shiftId' => $shiftId], 
            ['status' => $status] 
        );
    }
   public function getByScheduleAndShift($workScheduleId, $shiftId)
    {
        return WorkScheduleDetail::where('workScheduleId', $workScheduleId)
            ->where('shiftId', $shiftId)
            ->first();
    }

   
   
 }