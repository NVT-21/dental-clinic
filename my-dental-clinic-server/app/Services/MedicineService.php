<?php 
namespace App\Services;

use App\Repositories\MedicineRepository;
use Illuminate\Support\Facades\Hash;
use App\Models\Prescription;
use App\Models\MedicalExam;
use App\Models\Medicine;
use Illuminate\Support\Facades\DB;
class MedicineService extends BaseService
{
    protected $MedicineRepository;

    public function __construct(MedicineRepository $MedicineRepository)
    {
        $this->MedicineRepository = $MedicineRepository;
        parent::__construct();
    }
    public function getRepository()
    {
        return $this->MedicineRepository;
    }
   public function findByName($name)
   {
    return $this->MedicineRepository->findByName($name);
   }
   public function getValidMedincineQuantity($key)
   {
    return $this->MedicineRepository->getValidMedicineQuantity(1);
   }
   public function prescribeMedicine($idMedicalExam, $medicines)
   {
       DB::beginTransaction();
   
       try {
           $medicalExam = MedicalExam::findOrFail($idMedicalExam);
           $prescription=Prescription::where("idMedicalExam",$idMedicalExam)->first();
           if(!$prescription)
           {
            $prescription = Prescription::create([
                'idMedicalExam' => $idMedicalExam
            ]);
    
           }
        
           foreach ($medicines as $medicine) {
            $medicineId = $medicine['key'];
            $quantity = $medicine['quantity'];
            $name = $medicine['name'];
            $quantityToDeduct=$quantity;
            $medicineModel = Medicine::findOrFail($medicineId);
        
            $validMedicineQuantity = $this->MedicineRepository->getValidMedicineQuantity($medicineId);
            if ($validMedicineQuantity < $quantity) {
                throw new \Exception("Not enough quantity of medicine '{$name}' in stock.");
            }
        
            $existing = $prescription->medicines()->where('medicine_id', $medicineId)->first();

            if ($existing) {
                $currentQty = $existing->pivot->quantity;
                $quantityToDeduct -= $currentQty;
                $currentTotalPrice = $existing->pivot->total_price ?? 0;
                $totalPrice = $this->MedicineRepository->deductExpiredFirstStock($medicineId, $quantityToDeduct);
                $newTotalPrice = $currentTotalPrice + $totalPrice;
                $prescription->medicines()->updateExistingPivot($medicineId, [
                    'quantity' => $quantity,
                    'total_price' => $newTotalPrice
                ]);
            } else {
                $totalPrice = $this->MedicineRepository->deductExpiredFirstStock($medicineId, $quantityToDeduct);
            
                $prescription->medicines()->attach($medicineId, [
                    'quantity' => $quantity,
                    'total_price' => $totalPrice
                ]);
            }
            
        }
        
           DB::commit();
   
       } catch (\Exception $e) {
           DB::rollBack();
           return response()->json([
               'error' => 'Prescription failed: ' . $e->getMessage()
           ], 400);
       }
   }
   public function getMedicinesByMedicalExam($idMedicalExam)
   {
       return $this->MedicineRepository->getMedicinesByMedicalExam($idMedicalExam);
   }
}