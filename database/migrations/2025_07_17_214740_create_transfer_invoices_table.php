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
        Schema::create('transfer_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_center_id')->constrained('centers')->cascadeOnDelete();
            $table->foreignId('to_center_id')->constrained('centers')->cascadeOnDelete();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_invoices');
    }
};
