<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicApplication;
use App\Models\Doctor;
use App\Models\PlatformSetting;
use App\Models\QueueToken;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\View\View;

class OwnerDashboardController extends Controller
{
    public function overview(): View
    {
        return $this->renderSection('overview');
    }

    public function clinics(): View
    {
        return $this->renderSection('clinics');
    }

    public function applications(): View
    {
        return $this->renderSection('applications');
    }

    public function users(): View
    {
        return $this->renderSection('users');
    }

    public function settings(): View
    {
        return $this->renderSection('settings');
    }

    private function renderSection(string $activeOwnerSection): View
    {
        $trendLabels = [];
        $appointmentTrend = [];
        $userTrend = [];

        foreach (CarbonPeriod::create(now()->subDays(6)->startOfDay(), now()->startOfDay()) as $day) {
            $trendLabels[] = $day->format('d M');
            $appointmentTrend[] = Appointment::whereDate('appointment_date', $day->toDateString())->count();
            $userTrend[] = User::whereDate('created_at', $day->toDateString())->count();
        }

        $stats = [
            'clinics' => Clinic::count(),
            'pending_apps' => ClinicApplication::where('status', ClinicApplication::STATUS_PENDING)->count(),
            'clinic_admins' => User::where('role', User::ROLE_ADMIN)->count(),
            'doctors' => User::where('role', User::ROLE_DOCTOR)->count(),
            'helpers' => User::where('role', User::ROLE_HELPER)->count(),
            'patients' => User::where('role', User::ROLE_PATIENT)->count(),
        ];

        return view('dashboards.super-admin', [
            'activeOwnerSection' => $activeOwnerSection,
            'platformSettings' => PlatformSetting::firstOrCreate(
                ['id' => 1],
                [
                    'platform_name' => 'MediQueue',
                    'support_email' => 'support@mediqueue.test',
                    'commission_percent' => 10,
                    'queue_alert_threshold' => 2,
                    'clinic_verification_policy' => 'Verify clinic license, operating address, admin contact details, and service information before approval.',
                    'owner_notes' => 'Use this page to control business rules and support information shown across the platform.',
                ]
            ),
            'areas' => Area::orderBy('name')->get(),
            'clinicApplications' => ClinicApplication::with('area')
                ->where('status', ClinicApplication::STATUS_PENDING)
                ->latest()
                ->get(),
            'clinics' => Clinic::with(['area', 'users'])->latest()->get(),
            'doctors' => Doctor::with(['user', 'clinic'])->latest()->get(),
            'users' => User::with(['clinic', 'assignedDoctor.user'])->latest()->get(),
            'stats' => $stats,
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
                'labels' => $trendLabels,
                'appointments' => $appointmentTrend,
                'users' => $userTrend,
                'roles' => [
                    'labels' => ['Admins', 'Doctors', 'Helpers', 'Patients'],
                    'values' => [$stats['clinic_admins'], $stats['doctors'], $stats['helpers'], $stats['patients']],
                ],
            ],
        ]);
    }
}
