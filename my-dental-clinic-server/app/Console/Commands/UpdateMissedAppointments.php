<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateMissedAppointments extends Command
{
    protected $signature = 'appointments:update-missed';
    protected $description = 'Update status of appointments that were missed';

    public function handle()
    {
        try {
            $today = Carbon::now()->startOfDay();
            $yesterday = $today->copy()->subDay();

            // Lấy tất cả cuộc hẹn của ngày hôm qua có status là Confirmed và chưa được check-in
            $missedAppointments = Appointment::where('status', 'Confirmed')
                ->whereDate('bookingDate', $yesterday)
                ->where('is_done', false)
                ->get();

            $count = 0;
            foreach ($missedAppointments as $appointment) {
                $appointment->update([
                    'status' => 'No_Show',
                    'message' => 'Patient did not show up for the appointment'
                ]);

                // Ghi log thay đổi trạng thái
                AppointmentLog::create([
                    'appointment_id' => $appointment->id,
                    'status' => 'No_Show',
                    'employee_id' => null, // Vì đây là thay đổi tự động nên không có employee
                    'created_at' => now()
                ]);

                $count++;
            }

            Log::info("Updated {$count} missed appointments for date: {$yesterday->format('Y-m-d')}");
            $this->info("Successfully updated {$count} missed appointments.");

        } catch (\Exception $e) {
            Log::error("Error updating missed appointments: " . $e->getMessage());
            $this->error("Failed to update missed appointments: " . $e->getMessage());
        }
    }
} 