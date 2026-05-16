<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('appointment_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('amount');
            $table->string('currency', 3)->default('PKR');
            $table->string('gateway', 40)->default('local_test');
            $table->string('status', 30)->default('pending');
            $table->string('provider_reference')->nullable()->unique();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'status']);
            $table->index(['clinic_id', 'status']);
            $table->index(['doctor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_payments');
    }
};
