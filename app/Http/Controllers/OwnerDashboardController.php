<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Clinic;
use App\Models\ClinicApplication;
use App\Models\PlatformSetting;
use App\Models\User;
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
}
