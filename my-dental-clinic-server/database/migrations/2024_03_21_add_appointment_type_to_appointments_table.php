<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('appointment_type', ['consultation', 'treatment'])->default('consultation')->after('status');
            $table->text('symptoms')->nullable();
            $table->text('notes')->nullable()->after('symptoms');
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('appointment_type');
            $table->dropColumn('symptoms');
            $table->dropColumn('notes');
        });
    }
}; 