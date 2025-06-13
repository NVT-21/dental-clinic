<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RoomService;
class RoomController extends ApiResponseController
{
    protected $RoomService ;
    public function __construct(RoomService $RoomService )
    {
        $this->RoomService = $RoomService;
    }
    public function store(Request $request)
    {
        $result=$this->RoomService->createOrUpdate([
            'id'=>$request->input('id'),
            "name"=> $request->input("name"),
            'quantity'=>$request->input("quantity")
        ]);
        if($result['success'])
        {
            return $this->success($result['message']);
        }
        else {
            return $this->error($result['message']);
        }
    }
    public function paging(Request $request)
    {
        $input=$request->all();
        return $this->RoomService->paging($input);
    }
    public function getDoctorInRoom($id)
    {
       
        $doctorInRoom= $this->RoomService->getDoctorInRoom($id);
        $doctorWithoutRoom=$this->getDoctorsWithoutRoom();
        return [
            "doctorInRoom"=>$doctorInRoom,
            "doctorWithoutRoom"=>$doctorWithoutRoom
        ];
    }
    public function updateDoctorsToRoom(Request $request)
    {
        $idRoom=$request->input("idRoom");
        $idDoctors=$request->input("idDoctors");
        $result=$this->RoomService->updateDoctorsToRoom($idRoom,$idDoctors);
        if($result['success'])
        {
            return $this->success($result['message']);
        }
        else {
            return $this->error($result['message']);
        }
    }
    
}
