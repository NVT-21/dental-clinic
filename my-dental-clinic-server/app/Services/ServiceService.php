<?php 
namespace App\Services;

use App\Repositories\ServiceRepository;
use Illuminate\Support\Facades\Hash;

class ServiceService extends BaseService
{
    protected $ServiceRepository;

    public function __construct(ServiceRepository $ServiceRepository)
    {
        $this->ServiceRepository = $ServiceRepository;
        parent::__construct();
    }
    public function getRepository()
    {
        return $this->ServiceRepository;
    }
    public function getByName($name){
        return $this->ServiceRepository->findServiceByName($name);
    }
   
}