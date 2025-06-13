<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('appointment_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade'); // Thời gian ước lượng cho dịch vụ này
            $table->text('notes')->nullable(); // Ghi chú riêng cho dịch vụ này
            $table->timestamps();

            // Đảm bảo không có cặp appointment_id và service_id trùng lặp
            $table->unique(['appointment_id', 'service_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointment_services');
    }
}; 