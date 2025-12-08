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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('address');
            $table->string('contact_number');
            $table->string('age');
            $table->string('sex');

            // $table->string('frame_type')->nullable();
            // $table->string('color')->nullable();
            // $table->string('lens_supply')->nullable();
            $table->string('diagnosis')->nullable();

            $table->string('special_instructions')->nullable();
            $table->timestamp('follow_up_on')->nullable();

            // $table->decimal('amount', 10, 2)->nullable();
            // $table->decimal('deposit', 10, 2)->nullable();
            // $table->decimal('balance', 10, 2)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
