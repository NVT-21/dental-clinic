<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idPatient');
            $table->date('bookingDate')->nullable();
            $table->time('appointmentTime')->nullable();
            $table->string('status')->default('Pending')->nullable();

            // Các cột thêm sau
            $table->enum('appointment_type', ['consultation', 'treatment'])->default('consultation');
            $table->text('symptoms')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('locked_by')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('actual_arrival_time')->nullable();
            $table->enum('arrival_status', ['early', 'on_time', 'late'])->nullable();
             $table->integer('estimated_duration')->nullable()->comment('Tổng thời gian dự kiến của cuộc hẹn (tính bằng phút)');
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('idPatient')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('locked_by')->references('id')->on('employees')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
