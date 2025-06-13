<?php

namespace App\Repositories;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
 class ServiceRepository extends BaseRepository
 {
    function getModel(){
        return Service::class ;
    }
    public function findServiceByName($name)
{
    return Service::where('serviceName', 'like', "%{$name}%")->get();
}

    


   
   
 }