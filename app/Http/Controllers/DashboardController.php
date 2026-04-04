<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Area;
use App\Models\Clinic;
use App\Models\ClinicApplication;
use App\Models\Doctor;
use App\Models\PlatformSetting;
use App\Models\Prescription;
use App\Models\QueueToken;
use App\Models\Review;
use App\Models\Specialization;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return view('dashboards.super-admin', [
                'activeOwnerSection' => 'overview',
                'platformSettings' => PlatformSetting::firstOrCreate(['id' => 1], [
                    'platform_name' => 'MediQueue',
                    'support_email' => 'support@mediqueue.test',
                    'commission_percent' => 10,
                    'queue_alert_threshold' => 2,
                    'clinic_verification_policy' => 'Verify clinic license, address, admin details, and clinic services before approval.',
                ]),
                'areas' => Area::orderBy('name')->get(),
                'clinicApplications' => ClinicApplication::with('area')->where('status', ClinicApplication::STATUS_PENDING)->latest()->get(),
                'clinics' => Clinic::with(['area', 'users'])->latest()->get(),
                'users' => User::with('clinic')->latest()->get(),
                'stats' => [
                    'clinics' => Clinic::count(),
                    'pending_apps' => ClinicApplication::where('status', ClinicApplication::STATUS_PENDING)->count(),
                    'clinic_admins' => User::where('role', User::ROLE_ADMIN)->count(),
                    'doctors' => User::where('role', User::ROLE_DOCTOR)->count(),
                    'patients' => User::where('role', User::ROLE_PATIENT)->count(),
                ],
            ]);
        }

        if ($user->isAdmin()) {
            $clinic = $user->clinic()->with(['area'])->first();
            $monthStart = now()->startOfMonth()->toDateString();
            $monthEnd = now()->endOfMonth()->toDateString();
            $trendPeriod = CarbonPeriod::create(now()->subDays(6)->startOfDay(), now()->startOfDay());
            $trendLabels = [];
            $appointmentTrend = [];
            $prescriptionTrend = [];

            foreach ($trendPeriod as $day) {
                $trendLabels[] = $day->format('d M');
                $appointmentTrend[] = Appointment::where('clinic_id', $user->clinic_id)
                    ->whereDate('appointment_date', $day->toDateString())
                    ->count();
                $prescriptionTrend[] = Prescription::whereHas('doctor', fn ($query) => $query->where('clinic_id', $user->clinic_id))
                    ->whereDate('issued_at', $day->toDateString())
                    ->count();
            }

            return view('dashboards.admin', [
                'activeAdminSection' => 'reports',
                'clinic' => $clinic,
                'doctors' => Doctor::query()
                    ->with(['user', 'specialization', 'schedules' => fn ($query) => $query->orderBy('weekday')])
                    ->where('clinic_id', $user->clinic_id)
                    ->latest()
                    ->get(),
                'specializations' => Specialization::orderBy('name')->get(),
                'stats' => [
                    'doctors' => Doctor::where('clinic_id', $user->clinic_id)->count(),
                    'appointments_today' => Appointment::where('clinic_id', $user->clinic_id)
                        ->whereDate('appointment_date', now()->toDateString())
                        ->count(),
                    'appointments_month' => Appointment::where('clinic_id', $user->clinic_id)
                        ->whereBetween('appointment_date', [$monthStart, $monthEnd])
                        ->count(),
                    'waiting_tokens' => QueueToken::where('clinic_id', $user->clinic_id)
                        ->where('status', QueueToken::STATUS_WAITING)
                        ->count(),
                    'prescriptions' => Prescription::whereHas('doctor', fn ($query) => $query->where('clinic_id', $user->clinic_id))->count(),
                ],
                'trendLabels' => $trendLabels,
                'appointmentTrend' => $appointmentTrend,
                'prescriptionTrend' => $prescriptionTrend,
            ]);
        }

        if ($user->isDoctor()) {
            $doctor = $user->doctorProfile()->with(['clinic', 'specialization'])->firstOrFail();

            return view('dashboards.doctor', [
                'activeDoctorSection' => 'queue',
                'doctor' => $doctor,
                'appointments' => Appointment::query()
                    ->with(['patient', 'queueToken', 'consultation', 'prescription'])
                    ->where('doctor_id', $doctor->id)
                    ->whereDate('appointment_date', now()->toDateString())
                    ->orderBy('id')
                    ->get(),
            ]);
        }

        return view('dashboards.patient', [
            'activePatientSection' => 'appointments',
            'areas' => Area::orderBy('name')->get(),
            'specializations' => Specialization::orderBy('name')->get(),
            'appointments' => Appointment::query()
                ->with(['clinic.area', 'doctor.user', 'doctor.specialization', 'queueToken', 'consultation', 'prescription'])
                ->where('patient_id', $user->id)
                ->latest()
                ->take(10)
                ->get(),
            'platformReviews' => Review::query()
                ->with('user')
                ->where('review_type', Review::TYPE_PLATFORM)
                ->latest()
                ->take(6)
                ->get(),
            'myPlatformReview' => Review::where('user_id', $user->id)
                ->where('review_type', Review::TYPE_PLATFORM)
                ->whereNull('doctor_id')
                ->first(),
        ]);
    }
}
