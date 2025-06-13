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
        Schema::create('medicine_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('import_history_id');
            $table->unsignedBigInteger('medicine_id');
            $table->dateTime('expiration_date');
            $table->decimal('cost_price', 18, 2);
            $table->decimal('selling_price', 18, 2);
            $table->integer('initial_quantity');
            $table->integer('remaining_quantity');
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('import_history_id')->references('id')->on('import_histories')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_batches');
    }
};
