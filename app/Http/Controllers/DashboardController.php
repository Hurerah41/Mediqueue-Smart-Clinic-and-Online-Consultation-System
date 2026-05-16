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
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            $ownerTrendLabels = [];
            $ownerAppointmentTrend = [];
            $ownerUserTrend = [];

            foreach (CarbonPeriod::create(now()->subDays(6)->startOfDay(), now()->startOfDay()) as $day) {
                $ownerTrendLabels[] = $day->format('d M');
                $ownerAppointmentTrend[] = Appointment::whereDate('appointment_date', $day->toDateString())->count();
                $ownerUserTrend[] = User::whereDate('created_at', $day->toDateString())->count();
            }

            $ownerStats = [
                'clinics' => Clinic::count(),
                'pending_apps' => ClinicApplication::where('status', ClinicApplication::STATUS_PENDING)->count(),
                'clinic_admins' => User::where('role', User::ROLE_ADMIN)->count(),
                'doctors' => User::where('role', User::ROLE_DOCTOR)->count(),
                'helpers' => User::where('role', User::ROLE_HELPER)->count(),
                'patients' => User::where('role', User::ROLE_PATIENT)->count(),
            ];

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
                'doctors' => Doctor::with(['user', 'clinic'])->latest()->get(),
                'users' => User::with(['clinic', 'assignedDoctor.user'])->latest()->get(),
                'stats' => $ownerStats,
                'ownerMetrics' => [
                    'active_clinics' => Clinic::where('is_active', true)->count(),
                    'inactive_clinics' => Clinic::where('is_active', false)->count(),
                    'appointments_today' => Appointment::whereDate('appointment_date', now()->toDateString())->count(),
                    'waiting_tokens' => QueueToken::whereDate('queue_date', now()->toDateString())->where('status', QueueToken::STATUS_WAITING)->count(),
                    'serving_tokens' => QueueToken::whereDate('queue_date', now()->toDateString())->where('status', QueueToken::STATUS_CALLED)->count(),
                    'completed_tokens' => QueueToken::whereDate('queue_date', now()->toDateString())->where('status', QueueToken::STATUS_COMPLETED)->count(),
                    'verified_users' => User::where('is_verified', true)->count(),
                    'unverified_users' => User::where('is_verified', false)->count(),
                    'unassigned_helpers' => User::where('role', User::ROLE_HELPER)->whereNull('doctor_id')->count(),
                ],
                'ownerCharts' => [
                    'labels' => $ownerTrendLabels,
                    'appointments' => $ownerAppointmentTrend,
                    'users' => $ownerUserTrend,
                    'roles' => [
                        'labels' => ['Admins', 'Doctors', 'Helpers', 'Patients'],
                        'values' => [$ownerStats['clinic_admins'], $ownerStats['doctors'], $ownerStats['helpers'], $ownerStats['patients']],
                    ],
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
                    ->with(['user', 'specialization', 'helpers', 'schedules' => fn ($query) => $query->orderBy('weekday')])
                    ->where('clinic_id', $user->clinic_id)
                    ->latest()
                    ->get(),
                'helpers' => User::query()
                    ->where('role', User::ROLE_HELPER)
                    ->where('clinic_id', $user->clinic_id)
                    ->orderBy('name')
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

        if ($user->isHelper()) {
            return redirect()->route('helper.dashboard');
        }

        return view('dashboards.patient', [
            'activePatientSection' => 'appointments',
            'areas' => Area::orderBy('name')->get(),
            'specializations' => Specialization::orderBy('name')->get(),
            'appointments' => Appointment::query()
                ->with(['clinic.area', 'doctor.user', 'doctor.specialization', 'queueToken', 'payment', 'consultation', 'prescription'])
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
