<?php 
namespace App\Services;

use App\Repositories\MedicalExamServiceRepository;
use Illuminate\Support\Facades\Hash;

class MedicalExamServiceService extends BaseService
{
    protected $MedicalExamServiceRepository;

    public function __construct(MedicalExamServiceRepository $MedicalExamServiceRepository)
    {
        $this->MedicalExamServiceRepository = $MedicalExamServiceRepository;
        parent::__construct();
    }
    public function getRepository()
    {
        return $this->MedicalExamServiceRepository;
    }
    public function store ($data,$idMedicalExam)
    {
        return $this->MedicalExamServiceRepository->store($data,$idMedicalExam);
    }
    public function getServiceByIdMedicalExam($id)
    {
        return $this->MedicalExamServiceRepository->getServiceByIdMedicalExam($id);
    }
    public function deleteService($data)
    {
        return $this->MedicalExamServiceRepository->deleteService($data);
    }
}