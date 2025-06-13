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
        Schema::create('medical_exams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idEmployee'); // Liên kết với Employee (bác sĩ)
            $table->unsignedBigInteger('idAppointment'); // Liên kết với Appointment (lịch hẹn)
            $table->text('symptoms')->nullable(); // Triệu chứng bệnh nhân mô tả
            $table->string('status')->default('pending'); // Trạng thái ca khám (pending, completed, cancelled)
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('idEmployee')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('idAppointment')->references('id')->on('appointments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_exams');
    }
};
