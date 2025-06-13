<?php

namespace App\Repositories;
use App\Models\Room;
use App\Models\Employee;
 class RoomRepository extends BaseRepository
 {
    function getModel(){
        return Room::class ;
    }
    public function getDoctorInRoom($room)
    {
        return Room::with(['employees' => function ($query) {
            $query->where('role', 'Doctor')
                  ->where('status', 'working'); // thêm điều kiện status
        }])
        ->where('id', $room)
        ->first();
    }
    
    
   public function updateDoctorsToRoom($idRoom, $idDoctors)
{
   
    if (!is_array($idDoctors)) {
        $idDoctors = [$idDoctors];
    }
    if($idRoom==null)
    {
        Employee::whereIn('id', $idDoctors)->update(['idRoom' => null]);
        return [
            "success" => true,
            "message" => "Doctors deleted to room successfully",
        ];
    }
    // Lấy thông tin phòng
    $room = $this->getById($idRoom);
    if (!$room) {
        return [
            "success" => false,
            "message" => "Room not found",
        ];
    }

    $quantity = $room->quantity; 
    $currentCount = $room->employees()->count(); 
    $numberOfDoctorsToAdd = count($idDoctors); 

    
    if ($currentCount + $numberOfDoctorsToAdd > $quantity) {
        return [
            "success" => false,
            "message" => "Room is full. Cannot add more doctors.",
        ];
    }

    // Thêm nhân viên vào phòng
    try {
        Employee::whereIn('id', $idDoctors)->update(['idRoom' => $idRoom]);
        return [
            "success" => true,
            "message" => "Doctors added to room successfully",
        ];
    } catch (\Exception $e) {
        return [
            "success" => false,
            "message" => "Failed to add doctors to room: " . $e->getMessage(),
        ];
    }
} 
   

   
   
   
 }