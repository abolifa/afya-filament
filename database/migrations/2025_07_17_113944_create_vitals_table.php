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
        Schema::create('vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->date('recorded_at');
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('systolic', 5, 2)->nullable();
            $table->decimal('diastolic', 5, 2)->nullable();
            $table->decimal('heart_rate', 5, 2)->nullable();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('oxygen_saturation', 5, 2)->nullable();
            $table->decimal('sugar_level', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vitals');
    }
};
