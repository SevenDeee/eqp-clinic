<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('order_number');
            $table->enum('order_status', ['new', 'processing', 'claimable', 'completed', 'cancelled'])->default('new');
            // $table->integer('total_price');

            // $table->string('frame_type')->nullable();
            // $table->string('color')->nullable();
            // $table->string('lens_supply')->nullable();

            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('deposit', 10, 2)->nullable();
            $table->decimal('balance', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
