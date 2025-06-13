<?php 
namespace App\Services;

use App\Repositories\MedicalExamRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Notification;
use App\Events\MedicalExamCompleted;

class MedicalExamService extends BaseService
{
    protected $MedicalExamRepository;

    public function __construct(MedicalExamRepository $MedicalExamRepository)
    {
        $this->MedicalExamRepository = $MedicalExamRepository;
        parent::__construct();
    }
    public function getRepository()
    {
        return $this->MedicalExamRepository;
    }
    public function createMedicalExam($data,$creator)
    {
        return $this->MedicalExamRepository->createMedicalExam($data,$creator);
    }
    public function getMedicalExam($perPage,$status,$statusPayment,$idEmployee)
    {
        return $this->MedicalExamRepository->getMedicalExam($perPage,$status,$statusPayment,$idEmployee);
    }
    public function saveDoctorConclusion($data)
    {
        return $this->MedicalExamRepository->saveDoctorConclusion($data);
    }
    public function getPrescriptionAndService($idMedicalExam)
{
    return $this->MedicalExamRepository->getPrescriptionAndService($idMedicalExam);
}
    public function updateMedicalExam($idMedicalExam, $data)
    {
        try {
            $updated = $this->MedicalExamRepository->updateMedicalExam($idMedicalExam, $data);

            // Kiểm tra nếu trạng thái được cập nhật thành "Completed"
            if (isset($data['status']) && $data['status'] === 'Completed' && $updated['data']->status === 'Completed') {
                // Gửi thông báo cho người tạo ca khám
                if ($updated['data']->createdById) {
                    Log::info('Creating notification for medical exam completion', [
                        'createdById' => $updated['data']->createdById,
                        'patientName' => $updated['data']->appointment->patient->fullname
                    ]);

                    $notification = Notification::create([
                        'idEmployee' => $updated['data']->createdById,
                        'message' => "The medical examination for patient {$updated['data']->appointment->patient->fullname} has been successfully completed on " . now()->toDateString(),
                    ]);
                    
                    // Broadcast the notification
                    Log::info('Broadcasting MedicalExamCompleted event', [
                        'notification' => $notification
                    ]);
                    broadcast(new MedicalExamCompleted($notification))->toOthers();
                }
            }

            return $updated;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            throw $e; // Ném lỗi để repository xử lý rollback
        }
    }
}