<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('medical_exams', function (Blueprint $table) {
            $table->string('statusPayment')->default('Unpaid');
            $table->date('ExamDate')->default(now()); // Mặc định là ngày hôm nay
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_exams', function (Blueprint $table) {
            //
        });
    }
};
