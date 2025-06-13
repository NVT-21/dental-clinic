<?php 
namespace App\Services;

use App\Repositories\MedicineBatchRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Throwable;

class MedicineBatchService extends BaseService
{
    protected $MedicineBatchRepository;

    public function __construct(MedicineBatchRepository $MedicineBatchRepository)
    {
        $this->MedicineBatchRepository = $MedicineBatchRepository;
        parent::__construct();
    }
    public function getRepository()
    {
        return $this->MedicineBatchRepository;
    }
    public function createMedicineBatch($medicineBatch ,$importHistory)
    {
        DB::beginTransaction();
        try
        {
            
            foreach ($medicineBatch as $batch) {
                // Gọi service để tạo từng lô thuốc
                $this->MedicineBatchRepository->create([
                    'import_history_id' => $importHistory->id,
                    'medicine_id' => $batch['key'],
                    'expiration_date' =>Carbon::parse($batch['expiryDate'])->format('Y-m-d H:i:s'),
                    'cost_price' => $batch['convertedImportPrice'],
                    'selling_price' => $batch['sellingPrice'],
                    'initial_quantity' => $batch['total'],
                    'remaining_quantity' => $batch['total'],
                ]);
            }
            DB::commit();
            return [
                "success"=> true,
                "message"=>"Successfully created"
            ];
        }
        catch(Throwable $e)
        {
            Log::error($e->getMessage());
            DB::rollBack();
            return [
                "success"=> false,
                "message"=>"Failed to create"
            ];
        }
       
    }
    public function getMedicineBatchs($pageSize ,$keyword)
    {
        return $this->MedicineBatchRepository->getMedicineBatch($pageSize,$keyword) ;
    }
}