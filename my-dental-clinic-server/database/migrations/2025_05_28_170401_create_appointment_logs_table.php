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
        Schema::create('appointment_logs', function (Blueprint $table) {
         $table->id();
            $table->unsignedBigInteger('appointment_id');
            $table->string('status'); // pending, confirmed, cancelled, etc.
            $table->unsignedBigInteger('employee_id')->nullable(); // Người thực hiện (Lễ tân/Admin), có thể null
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_logs');
    }
};
