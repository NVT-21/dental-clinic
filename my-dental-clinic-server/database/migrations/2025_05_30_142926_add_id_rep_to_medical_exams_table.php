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
        Schema::table('medical_exams', function (Blueprint $table) {
            $table->unsignedBigInteger('createdById')->after('idAppointment')->nullable(); // Lễ tân tạo ca khám, có thể null
            $table->foreign('createdById')->references('id')->on('employees')->onDelete('set null'); // Liên kết với employees, đặt null khi xóa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_exams', function (Blueprint $table) {
            $table->dropForeign(['createdById']);
            $table->dropColumn('createdById');
        });
    }
};
