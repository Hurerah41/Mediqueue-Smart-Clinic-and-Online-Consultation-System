<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinics', function (Blueprint $table): void {
            $table->index(['is_active', 'area_id'], 'clinics_active_area_index');
            $table->index('name', 'clinics_name_index');
        });

        Schema::table('doctors', function (Blueprint $table): void {
            $table->index(['clinic_id', 'specialization_id'], 'doctors_clinic_specialization_index');
        });

        Schema::table('appointments', function (Blueprint $table): void {
            $table->index(['doctor_id', 'appointment_date', 'status'], 'appointments_doctor_date_status_index');
            $table->index(['clinic_id', 'appointment_date'], 'appointments_clinic_date_index');
            $table->index(['patient_id', 'status'], 'appointments_patient_status_index');
        });

        Schema::table('queue_tokens', function (Blueprint $table): void {
            $table->index(['doctor_id', 'queue_date', 'status'], 'queue_tokens_doctor_date_status_index');
            $table->index(['clinic_id', 'queue_date'], 'queue_tokens_clinic_date_index');
        });

        Schema::table('consultation_messages', function (Blueprint $table): void {
            $table->index(['consultation_id', 'created_at'], 'consultation_messages_consultation_created_index');
        });

        Schema::table('app_notifications', function (Blueprint $table): void {
            $table->index(['user_id', 'read_at', 'created_at'], 'app_notifications_user_read_created_index');
        });

        Schema::table('reviews', function (Blueprint $table): void {
            $table->index(['review_type', 'doctor_id', 'rating'], 'reviews_type_doctor_rating_index');
        });

        Schema::table('clinic_applications', function (Blueprint $table): void {
            $table->index(['status', 'area_id'], 'clinic_applications_status_area_index');
            $table->index('admin_email', 'clinic_applications_admin_email_index');
        });
    }

    public function down(): void
    {
        Schema::table('clinic_applications', function (Blueprint $table): void {
            $table->dropIndex('clinic_applications_status_area_index');
            $table->dropIndex('clinic_applications_admin_email_index');
        });

        Schema::table('reviews', function (Blueprint $table): void {
            $table->dropIndex('reviews_type_doctor_rating_index');
        });

        Schema::table('app_notifications', function (Blueprint $table): void {
            $table->dropIndex('app_notifications_user_read_created_index');
        });

        Schema::table('consultation_messages', function (Blueprint $table): void {
            $table->dropIndex('consultation_messages_consultation_created_index');
        });

        Schema::table('queue_tokens', function (Blueprint $table): void {
            $table->dropIndex('queue_tokens_doctor_date_status_index');
            $table->dropIndex('queue_tokens_clinic_date_index');
        });

        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropIndex('appointments_doctor_date_status_index');
            $table->dropIndex('appointments_clinic_date_index');
            $table->dropIndex('appointments_patient_status_index');
        });

        Schema::table('doctors', function (Blueprint $table): void {
            $table->dropIndex('doctors_clinic_specialization_index');
        });

        Schema::table('clinics', function (Blueprint $table): void {
            $table->dropIndex('clinics_active_area_index');
            $table->dropIndex('clinics_name_index');
        });
    }
};
