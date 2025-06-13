<?php

namespace App\Http\Controllers;

use App\Models\ImportHistory;
use Illuminate\Support\Facades\DB;
use App\Services\MedicineBatchService;
use App\Services\ImportHistoryService;
use Illuminate\Http\Request;

class MedicineBatchController extends ApiResponseController
{
    protected $MedicineBatchService ,$ImportHistoryService ;
    public function __construct(MedicineBatchService $MedicineBatchService , ImportHistoryService $ImportHistoryService )
    {
        $this->MedicineBatchService = $MedicineBatchService;
        $this->ImportHistoryService=$ImportHistoryService;
    }
    public function createMedicineBatch(Request $request)
    {
        DB::beginTransaction(); // Bắt đầu transaction
        try {
            $medicineBatchs = $request->input("medicineBatch");
            // Tạo ImportHistory
            $newImportHistory = $this->ImportHistoryService->create([
                'import_date' => now(),
                'importer' => $this->getEmployee()->fullName
            ]);
    
            // Gọi service để tạo MedicineBatch
            $result = $this->MedicineBatchService->createMedicineBatch($medicineBatchs, $newImportHistory['data']);
    
            if ($result['success']) {
                DB::commit(); // Commit transaction nếu không có lỗi
                return $this->success($result['message']);
            } else {
                DB::rollBack(); // Rollback transaction nếu có lỗi
                return $this->error($result['message']);
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback nếu có bất kỳ lỗi nào
            return $this->error("An error occurred: " . $e->getMessage());
        }
    }
    public function getMedicineBatchs(Request $request)
    {
        $pageSize=$request->input("pageSize"); 
        $keyword=$request->input("keyword");
        return $this->MedicineBatchService->getMedicineBatchs($pageSize,$keyword);
    }
}
