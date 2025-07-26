<?php 
namespace App\Services;

use App\Repositories\AppointmentRepository;
use Illuminate\Support\Facades\Hash;

class AppointmentService extends BaseService
{
    protected $AppointmentRepository;

    public function __construct(AppointmentRepository $AppointmentRepository)
    {
        $this->AppointmentRepository = $AppointmentRepository;
        parent::__construct();
    }
    public function getRepository()
    {
        return $this->AppointmentRepository;
    }
    public function getAppointments($perPage, $keyword = null, $status = null, $role, $date = null)
    {
        return $this->AppointmentRepository->getAppointments($perPage, $keyword, $status, $role, $date);
    }
    public function updateAppointment($id, $data,$employee){
        return $this->AppointmentRepository->updateAppointment($id, $data,$employee);
    }
    public function getConfirmedAppointmentsInTimeRange($data)
    {
        return $this->AppointmentRepository->getConfirmedAppointmentsInTimeRange($data);
    }
    public function checkAndSuggestSlotWithSubBlock( $date,$startTime,$totalEstimatedDuration,$maxSuggestions)
    {
        return  $this->AppointmentRepository->checkAndSuggestSlotWithSubBlock( $date,$startTime,$totalEstimatedDuration,$maxSuggestions);
    }

 

 
}