<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->string('plan_name');
            $table->unsignedInteger('monthly_fee');
            $table->unsignedSmallInteger('doctor_limit')->default(5);
            $table->unsignedInteger('monthly_appointment_limit')->default(500);
            $table->string('status', 20)->default('active');
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->timestamp('last_billed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_subscriptions');
    }
};
