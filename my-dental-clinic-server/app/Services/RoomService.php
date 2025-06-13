<?php 
namespace App\Services;

use App\Repositories\RoomRepository;
use Illuminate\Support\Facades\Hash;

class RoomService extends BaseService
{
    protected $RoomRepository;

    public function __construct(RoomRepository $RoomRepository)
    {
        $this->RoomRepository = $RoomRepository;
        parent::__construct();
    }
    public function getRepository()
    {
        return $this->RoomRepository;
    }
    public function getDoctorInRoom($room)
    {
        return $this->RoomRepository->getDoctorInRoom($room);
    }
    public function updateDoctorsToRoom($idRoom,$idDoctors)
    {
        return $this->RoomRepository->updateDoctorsToRoom($idRoom,$idDoctors);
    }
   
   
    
}