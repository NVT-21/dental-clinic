<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Room;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\ImportHistory;
use App\Models\Appointment;
use App\Models\MedicalExam;
use App\Models\WorkShift;
use App\Models\WorkSchedule;
use App\Models\WorkScheduleDetail;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo Roles thủ công nếu không có factory
        $roles = collect([
            Role::firstOrCreate(['name' => 'admin']),
            Role::firstOrCreate(['name' => 'doctor']),
            Role::firstOrCreate(['name' => 'receptionist']),
        ]);

        // Tạo phòng
        $rooms = Room::factory()->count(5)->create();

        // Tạo User + Employee gắn role
        User::factory()->count(10)->create()->each(function ($user) use ($roles, $rooms) {
            $role = $roles->random();
            $employee = Employee::factory()->create([
                'user_id' => $user->id,
                'idRoom' => $rooms->random()->id,
                'role' => $role->name,
            ]);
            $user->roles()->attach($role);
        });

        // Tạo bệnh nhân
        $patients = Patient::factory()->count(20)->create();

        // Tạo dịch vụ
        $services = Service::factory()->count(10)->create();

        // Tạo thuốc + lô thuốc
        $medicines = Medicine::factory()->count(10)->create();
        $importHistories = ImportHistory::factory()->count(5)->create();
        foreach ($medicines as $medicine) {
            MedicineBatch::factory()->count(2)->create([
                'medicine_id' => $medicine->id,
                'import_history_id' => $importHistories->random()->id,
            ]);
        }

        // Tạo WorkShift (nếu chưa có)
        $shifts = WorkShift::factory()->count(3)->create();

        // Tạo WorkSchedule + WorkScheduleDetail với shiftId không trùng
        $employees = Employee::all();
        foreach ($employees as $employee) {
            $schedule = WorkSchedule::factory()->create([
                'idEmployee' => $employee->id,
            ]);

            // Random 2 shift khác nhau
            $randomShifts = $shifts->pluck('id')->shuffle()->take(2);
            foreach ($randomShifts as $shiftId) {
                WorkScheduleDetail::factory()->create([
                    'workScheduleId' => $schedule->id,
                    'shiftId' => $shiftId,
                ]);
            }
        }

        // Tạo lịch hẹn + khám bệnh
        foreach ($patients as $patient) {
            $appointment = Appointment::factory()->create([
                'idPatient' => $patient->id,
            ]);

            MedicalExam::factory()->create([
                'idAppointment' => $appointment->id,
                'idEmployee' => $employees->random()->id,
                'createdById' => $employees->random()->id,
            ]);
        }
    }
}
