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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('file_number')->unique();
            $table->string('national_id')->unique();
            $table->string('family_issue_number')->nullable();
            $table->string('name')->index();
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('email')->nullable()->unique();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('dob')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('image')->nullable();
            $table->boolean('verified')->default(false);
            $table->foreignId('center_id')->nullable()->constrained('centers')->nullOnDelete();
            $table->rememberToken();
            $table->softDeletes();
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
