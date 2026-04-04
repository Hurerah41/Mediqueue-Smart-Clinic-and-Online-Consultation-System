<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorDashboardPageController extends Controller
{
    public function queue(Request $request): View
    {
        return $this->renderSection($request, 'queue');
    }

    public function prescriptions(Request $request): View
    {
        return $this->renderSection($request, 'prescriptions');
    }

    private function renderSection(Request $request, string $activeDoctorSection): View
    {
        $doctor = $request->user()->doctorProfile()->with(['clinic', 'specialization'])->firstOrFail();

        return view('dashboards.doctor', [
            'activeDoctorSection' => $activeDoctorSection,
            'doctor' => $doctor,
            'appointments' => Appointment::query()
                ->with(['patient', 'queueToken', 'consultation', 'prescription'])
                ->where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', now()->toDateString())
                ->orderBy('id')
                ->get(),
        ]);
    }
}
