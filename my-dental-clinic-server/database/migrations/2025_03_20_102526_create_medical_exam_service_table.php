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
        Schema::create('medical_exam_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_exam_id');
            $table->unsignedBigInteger('service_id');
            $table->integer('quantity')->default(1); // Số lượng dịch vụ
            $table->text('content')->nullable(); // Nội dung thêm
            $table->integer('price'); // Giá dịch vụ đã áp dụng
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('medical_exam_id')->references('id')->on('medical_exams')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_exam_service');
    }
};
