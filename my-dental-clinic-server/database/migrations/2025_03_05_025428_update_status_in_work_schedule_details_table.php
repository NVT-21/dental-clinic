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
        Schema::table('work_schedule_details', function (Blueprint $table) {
            $table->enum('status', ['working', 'off'])->default('working')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_schedule_details', function (Blueprint $table) {
            $table->enum('status', ['present', 'absent'])->default('present')->change();
        });
    }
};
