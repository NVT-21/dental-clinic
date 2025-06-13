<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class WorkShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('work_shifts')->insert([
            [
                'shiftName' => 'Morning',
                'startTime' => '07:00:00',
                'endTime' => '11:30:00'
            ],
            [
                'shiftName' => 'Afternoon',
                'startTime' => '13:30:00',
                'endTime' => '17:30:00'
            ],
            [
                'shiftName' => 'Evening',
                'startTime' => '17:30:00',
                'endTime' => '21:00'
            ],
        ]);
    }
}
