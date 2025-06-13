<?php

namespace App\Repositories;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
 class MedicineRepository extends BaseRepository
 {
    function getModel(){
        return Medicine::class ;
    }
    public function findByName($name)
    {
        return Medicine::where("medicine_name", "LIKE", "%$name%")->get();
    }
    public function getValidMedicineQuantity($idMedicine)
    {
       $medicine = Medicine::with(['medicineBatches' ])->find($idMedicine);
    
       $totalAvailable = $medicine-> medicineBatches->sum('remaining_quantity');
    
        return $totalAvailable;
    }
    public function deductExpiredFirstStock($idMedicine, $quantity)
    {
        DB::beginTransaction(); // ⭐ Start transaction
    
        try {
            $medicine = Medicine::with(['medicineBatches' => function ($query) {
                $query->where('expiration_date', '>=', today())
                      ->orderBy('expiration_date', 'asc');
            }])->findOrFail($idMedicine);
    
            $remainingToDeduct = $quantity;
            $totalPrice=0;
            foreach ($medicine->medicineBatches as $batch) {
                if ($batch->remaining_quantity >= $remainingToDeduct) {
                    $batch->remaining_quantity -= $remainingToDeduct;
                    $totalPrice+=$remainingToDeduct* $batch->selling_price;
                    $batch->save();
                    $remainingToDeduct = 0;
                    break;
                } else {
                    $totalPrice += $batch->remaining_quantity * $batch->selling_price;
                    $remainingToDeduct -= $batch->remaining_quantity;
                    $batch->remaining_quantity = 0;
                    $batch->save();
                }
            }
    
            if ($remainingToDeduct > 0) {
                throw new \Exception("Not enough valid stock to deduct.");
            }
    
            DB::commit(); // ✅ Commit nếu trừ thành công
            return $totalPrice;
    
        } catch (\Exception $e) {
            DB::rollBack(); // ❌ Rollback nếu có lỗi
            throw $e;
        }
    }
    public function getMedicinesByMedicalExam($idMedicalExam)
    {
        $prescription = Prescription::where('idMedicalExam', $idMedicalExam)->firstOrFail();
        return $prescription->medicines;
    }
    



   
   
 }