<?php

use App\Http\Controllers\AdminDoctorController;
use App\Http\Controllers\AdminDoctorScheduleController;
use App\Http\Controllers\AdminClinicProfileController;
use App\Http\Controllers\AdminDashboardPageController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClinicApplicationController;
use App\Http\Controllers\ClinicBrowseController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardLiveController;
use App\Http\Controllers\DoctorQueueController;
use App\Http\Controllers\DoctorDashboardPageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\OwnerPlatformSettingController;
use App\Http\Controllers\PatientDashboardPageController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SuperAdminClinicApplicationController;
use App\Http\Controllers\SuperAdminClinicController;
use App\Http\Controllers\SuperAdminUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('verify-otp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify-otp.store');
    Route::post('/verify-otp/resend', [AuthController::class, 'resendOtp'])->name('verify-otp.resend');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetCode'])->name('password.email');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset.form');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::get('/clinics', [ClinicBrowseController::class, 'index'])->name('clinics.index');
Route::get('/clinics/{clinic:slug}', [ClinicBrowseController::class, 'show'])->name('clinics.show');
Route::get('/clinic-registration', [ClinicApplicationController::class, 'create'])->name('clinic-applications.create');
Route::post('/clinic-registration', [ClinicApplicationController::class, 'store'])->name('clinic-applications.store');

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/dashboard/live', DashboardLiveController::class)->name('dashboard.live');
    Route::get('/notifications/live', [NotificationController::class, 'index'])->name('notifications.live');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::post('/appointments', [AppointmentController::class, 'store'])
        ->middleware('role:patient')
        ->name('appointments.store');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments/{appointment}/status', [AppointmentController::class, 'status'])->name('appointments.status');

    Route::get('/consultations/{consultation}', [ConsultationController::class, 'show'])->name('consultations.show');
    Route::get('/consultations/{consultation}/messages', [ConsultationController::class, 'messages'])->name('consultations.messages.index');
    Route::post('/consultations/{consultation}/messages', [ConsultationController::class, 'sendMessage'])->name('consultations.messages.store');

    Route::get('/doctor/live-appointments', [DoctorQueueController::class, 'liveAppointments'])
        ->middleware('role:doctor')
        ->name('doctor.appointments.live');
    Route::get('/doctor/queue', [DoctorDashboardPageController::class, 'queue'])
        ->middleware('role:doctor')
        ->name('doctor.queue');
    Route::get('/doctor/prescriptions', [DoctorDashboardPageController::class, 'prescriptions'])
        ->middleware('role:doctor')
        ->name('doctor.prescriptions');
    Route::post('/doctor/appointments/{appointment}/call', [DoctorQueueController::class, 'callPatient'])
        ->middleware('role:doctor')
        ->name('doctor.appointments.call');
    Route::post('/doctor/appointments/{appointment}/complete', [DoctorQueueController::class, 'complete'])
        ->middleware('role:doctor')
        ->name('doctor.appointments.complete');
    Route::post('/doctor/appointments/{appointment}/prescriptions', [PrescriptionController::class, 'store'])
        ->middleware('role:doctor')
        ->name('doctor.prescriptions.store');
    Route::get('/prescriptions/{prescription}/download', [PrescriptionController::class, 'download'])
        ->name('prescriptions.download');
    Route::get('/prescriptions/{prescription}/image', [PrescriptionController::class, 'downloadImage'])
        ->name('prescriptions.image');

    Route::post('/admin/doctors', [AdminDoctorController::class, 'store'])
        ->middleware('role:admin')
        ->name('admin.doctors.store');
    Route::get('/admin/reports', [AdminDashboardPageController::class, 'reports'])
        ->middleware('role:admin')
        ->name('admin.reports');
    Route::get('/admin/doctors', [AdminDashboardPageController::class, 'doctors'])
        ->middleware('role:admin')
        ->name('admin.doctors.index');
    Route::get('/admin/settings', [AdminDashboardPageController::class, 'settings'])
        ->middleware('role:admin')
        ->name('admin.settings');
    Route::post('/admin/doctors/{doctor}/schedules', [AdminDoctorScheduleController::class, 'store'])
        ->middleware('role:admin')
        ->name('admin.doctors.schedules.store');
    Route::delete('/admin/doctors/{doctor}/schedules/{schedule}', [AdminDoctorScheduleController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('admin.doctors.schedules.destroy');
    Route::post('/admin/clinic-profile', [AdminClinicProfileController::class, 'update'])
        ->middleware('role:admin')
        ->name('admin.clinic-profile.update');

    Route::post('/owner/clinics', [SuperAdminClinicController::class, 'store'])
        ->middleware('role:super_admin')
        ->name('owner.clinics.store');
    Route::get('/owner/overview', [OwnerDashboardController::class, 'overview'])
        ->middleware('role:super_admin')
        ->name('owner.overview');
    Route::get('/owner/clinics', [OwnerDashboardController::class, 'clinics'])
        ->middleware('role:super_admin')
        ->name('owner.clinics');
    Route::get('/owner/applications', [OwnerDashboardController::class, 'applications'])
        ->middleware('role:super_admin')
        ->name('owner.applications');
    Route::get('/owner/users', [OwnerDashboardController::class, 'users'])
        ->middleware('role:super_admin')
        ->name('owner.users');
    Route::get('/owner/settings', [OwnerDashboardController::class, 'settings'])
        ->middleware('role:super_admin')
        ->name('owner.settings');
    Route::post('/owner/settings', [OwnerPlatformSettingController::class, 'update'])
        ->middleware('role:super_admin')
        ->name('owner.settings.update');
    Route::post('/owner/settings/test-email', [OwnerPlatformSettingController::class, 'sendTestEmail'])
        ->middleware('role:super_admin')
        ->name('owner.settings.test-email');
    Route::post('/owner/clinic-applications/{clinicApplication}/approve', [SuperAdminClinicApplicationController::class, 'approve'])
        ->middleware('role:super_admin')
        ->name('owner.clinic-applications.approve');
    Route::post('/owner/clinic-applications/{clinicApplication}/reject', [SuperAdminClinicApplicationController::class, 'reject'])
        ->middleware('role:super_admin')
        ->name('owner.clinic-applications.reject');
    Route::patch('/owner/users/{user}', [SuperAdminUserController::class, 'update'])
        ->middleware('role:super_admin')
        ->name('owner.users.update');

    Route::post('/ai/symptom-check', [AiAssistantController::class, 'symptomCheck'])
        ->middleware('role:patient')
        ->name('ai.symptom-check');
    Route::post('/ai/chatbot', [AiAssistantController::class, 'chatbot'])
        ->middleware('role:patient')
        ->name('ai.chatbot');
    Route::get('/patient/appointments', [PatientDashboardPageController::class, 'appointments'])
        ->middleware('role:patient')
        ->name('patient.appointments');
    Route::get('/patient/ai-tools', [PatientDashboardPageController::class, 'aiTools'])
        ->middleware('role:patient')
        ->name('patient.ai-tools');
    Route::get('/patient/reviews', [PatientDashboardPageController::class, 'reviews'])
        ->middleware('role:patient')
        ->name('patient.reviews');

    Route::post('/doctors/{doctor}/reviews', [ReviewController::class, 'storeDoctorReview'])
        ->middleware('role:patient')
        ->name('doctors.reviews.store');
    Route::post('/platform/reviews', [ReviewController::class, 'storePlatformReview'])
        ->middleware('role:patient')
        ->name('platform.reviews.store');
});
