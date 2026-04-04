<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Area;
use App\Models\Review;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientDashboardPageController extends Controller
{
    public function appointments(Request $request): View
    {
        return $this->renderSection($request, 'appointments');
    }

    public function aiTools(Request $request): View
    {
        return $this->renderSection($request, 'ai-tools');
    }

    public function reviews(Request $request): View
    {
        return $this->renderSection($request, 'reviews');
    }

    private function renderSection(Request $request, string $activePatientSection): View
    {
        $user = $request->user();

        return view('dashboards.patient', [
            'activePatientSection' => $activePatientSection,
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
