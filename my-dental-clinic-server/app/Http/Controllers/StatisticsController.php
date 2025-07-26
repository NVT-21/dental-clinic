<?php

namespace App\Http\Controllers;

use App\Models\MedicalExam;
use App\Models\Patient;
use App\Models\Employee;
use App\Models\MedicalExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function getStatistics(Request $request)
    {
        $period = $request->input('period', 'month'); // month, day, year
        $date = $request->input('date', now()->format('Y-m-d'));
        $startDate = null;
        $endDate = null;

        // Set date range based on period
        switch ($period) {
            case 'day':
                $startDate = Carbon::parse($date)->startOfDay();
                $endDate = Carbon::parse($date)->endOfDay();
                break;
            case 'month':
                $startDate = Carbon::parse($date)->startOfMonth();
                $endDate = Carbon::parse($date)->endOfMonth();
                break;
            case 'year':
                $startDate = Carbon::parse($date)->startOfYear();
                $endDate = Carbon::parse($date)->endOfYear();
                break;
        }

        // 1. Thống kê doanh thu
        $revenue = $this->calculateRevenue($startDate, $endDate);

        // 2. Thống kê số ca khám theo bác sĩ
        $doctorExams = $this->getDoctorExams($startDate, $endDate);

        // 3. Thống kê bệnh nhân
        $patientStats = $this->getPatientStatistics($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => [
                'revenue' => $revenue,
                'doctor_exams' => $doctorExams,
                'patient_statistics' => $patientStats
            ]
        ]);
    }

    private function calculateRevenue($startDate, $endDate)
    {
        // Tính doanh thu từ dịch vụ (price * quantity)
        $serviceRevenue = MedicalExamService::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('SUM(price * quantity) as total')
            ->value('total') ?? 0;  

        // Tính doanh thu từ thuốc (nếu có)
        $medicineRevenue = DB::table('medicine_prescription')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_price');

        return [
            'total' => $serviceRevenue + $medicineRevenue,
            'service_revenue' => $serviceRevenue,
            'medicine_revenue' => $medicineRevenue,
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ]
        ];
    }

    private function getDoctorExams($startDate, $endDate)
    {
        return Employee::where('role', 'Doctor')
            ->withCount(['medicalExams' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'Completed')
                      ->where('statusPayment', 'Paid')
                      ->whereBetween('ExamDate', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function ($doctor) {
                return [
                    'doctor_id' => $doctor->id,
                    'doctor_name' => $doctor->fullName,
                    'total_exams' => $doctor->medical_exams_count
                ];
            });
    }

    private function getPatientStatistics($startDate, $endDate)
{
    // Lấy tổng số ca khám đã hoàn thành (thay thế total_patients)
    $totalExams = MedicalExam::where('status', 'Completed')
        ->where('statusPayment', 'Paid')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    // Lấy số ca khám từ đặt lịch online (status = 'Confirmed')
    $onlineExams = MedicalExam::whereHas('appointment', function ($query) use ($startDate, $endDate) {
            $query->where('status', 'Confirmed');
        })
        ->where('status', 'Completed')
        ->where('statusPayment', 'Paid')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    // Lấy số ca khám trực tiếp (walk-in, status IS NULL)
    $walkInExams = MedicalExam::whereHas('appointment', function ($query) use ($startDate, $endDate) {
            $query->whereNull('status');
        })
        ->where('status', 'Completed')
        ->where('statusPayment', 'Paid')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    return [
        'total_exams' => $totalExams, // Tổng số ca khám đã hoàn thành
        'online_exams' => $onlineExams, // Số ca khám từ đặt lịch online
        'walk_in_exams' => $walkInExams, // Số ca khám trực tiếp
        'period' => [
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d')
        ]
    ];
}
} 