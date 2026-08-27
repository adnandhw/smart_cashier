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
        Schema::create('ai_detection_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->decimal('confidence', 5, 2); // e.g. 0.95 (95%)
            $table->integer('inference_time_ms')->nullable(); // e.g. 15ms
            $table->decimal('fps', 5, 2)->nullable(); // e.g. 30.00
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_detection_logs');
    }
};
