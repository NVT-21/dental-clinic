<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_schedule_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workScheduleId');
            $table->unsignedBigInteger('shiftId');
            $table->enum('status', ['present', 'absent'])->default('present');
            $table->timestamps();

            $table->foreign('workScheduleId')->references('id')->on('work_schedules')->onDelete('cascade');
            $table->foreign('shiftId')->references('id')->on('work_shifts')->onDelete('cascade');

            $table->unique(['workScheduleId', 'shiftId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedule_details');
    }
};
