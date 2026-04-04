<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->string('clinic_name');
            $table->string('clinic_phone', 30)->nullable();
            $table->string('address', 500);
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->string('admin_name');
            $table->string('admin_email');
            $table->string('admin_phone', 30)->nullable();
            $table->string('license_document_path')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('owner_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_applications');
    }
};
