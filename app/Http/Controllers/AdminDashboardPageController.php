<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Prescription;
use App\Models\QueueToken;
use App\Models\Specialization;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardPageController extends Controller
{
    public function reports(Request $request): View
    {
        return $this->renderSection($request, 'reports');
    }

    public function doctors(Request $request): View
    {
        return $this->renderSection($request, 'doctors');
    }

    public function settings(Request $request): View
    {
        return $this->renderSection($request, 'settings');
    }

    private function renderSection(Request $request, string $activeAdminSection): View
    {
        $user = $request->user();
        $clinic = $user->clinic()->with(['area'])->first();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $trendLabels = [];
        $appointmentTrend = [];
        $prescriptionTrend = [];

        foreach (CarbonPeriod::create(now()->subDays(6)->startOfDay(), now()->startOfDay()) as $day) {
            $trendLabels[] = $day->format('d M');
            $appointmentTrend[] = Appointment::where('clinic_id', $user->clinic_id)
                ->whereDate('appointment_date', $day->toDateString())
                ->count();
            $prescriptionTrend[] = Prescription::whereHas('doctor', fn ($query) => $query->where('clinic_id', $user->clinic_id))
                ->whereDate('issued_at', $day->toDateString())
                ->count();
        }

        return view('dashboards.admin', [
            'activeAdminSection' => $activeAdminSection,
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
}
