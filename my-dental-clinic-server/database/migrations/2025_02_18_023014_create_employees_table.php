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
        Schema::create('employees', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string("fullName", 255); 
            $table->date("birthday"); 
            $table->string("gender", 50); 
            $table->string("phoneNumber", 20);
            $table->string('role', 255)->nullable();
            $table->unsignedBigInteger('idRoom')->nullable(); 
            $table->foreign('idRoom')->references('id')->on('rooms')->onDelete('set null');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
