<?php 
namespace App\Services;

use App\Repositories\EmployeeRepository;
use Illuminate\Support\Facades\Hash;

class EmployeeService extends BaseService
{
    protected $EmployeeRepository;

    public function __construct(EmployeeRepository $EmployeeRepository)
    {
        $this->EmployeeRepository = $EmployeeRepository;
        parent::__construct();
    }
    public function getRepository()
    {
        return $this->EmployeeRepository;
    }
    public function createEmployee($employee)
    {
        $newEmployee=$this->EmployeeRepository->create($employee);
    }
    public function getDoctorsWithoutRoom(){
        return $this->EmployeeRepository->getDoctorsWithoutRoom();
    }
    public function getRoomOfDoctor($idDoctor)
    {
        return $this->EmployeeRepository->getRoomOfDoctor($idDoctor);
    }
    public function paging($input)
    {
        return $this->EmployeeRepository->getEmployees($input);
    }
}