<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('platform_name')->default('MediQueue');
            $table->string('support_email')->default('support@mediqueue.test');
            $table->unsignedTinyInteger('commission_percent')->default(10);
            $table->unsignedTinyInteger('queue_alert_threshold')->default(2);
            $table->text('clinic_verification_policy')->nullable();
            $table->text('owner_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
