<?php

namespace App\Http\Controllers;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $EmployeeService ;
    public function __construct(EmployeeService $EmployeeService )
    {
        $this->EmployeeService = $EmployeeService;
    }
    public function getDoctorWithoutRoom()
    {
        return $this->EmployeeService->getDoctorsWithoutRoom();
    }
    public function getRoomOfDoctor($id)
    {
        return $this->EmployeeService->getRoomOfDoctor($id);
    }
    public function paging (Request $request)
    {
        $input =$request->all();
        return $this->EmployeeService->paging($input);

    }
    
}
