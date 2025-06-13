<?php 
namespace App\Services;

use App\Repositories\ImportHistoryRepository;
use Illuminate\Support\Facades\Hash;

class ImportHistoryService extends BaseService
{
    protected $ImportHistoryRepository;

    public function __construct(ImportHistoryRepository $ImportHistoryRepository)
    {
        $this->ImportHistoryRepository = $ImportHistoryRepository;
        parent::__construct();
    }
    public function getRepository()
    {
        return $this->ImportHistoryRepository;
    }

}