<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Clinic;
use App\Models\Review;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicBrowseController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'specialization_id' => ['nullable', 'integer', 'exists:specializations,id'],
            'search' => ['nullable', 'string', 'max:120'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $areaId = isset($validated['area_id']) ? (int) $validated['area_id'] : null;
        $specializationId = isset($validated['specialization_id']) ? (int) $validated['specialization_id'] : null;
        $searchTerm = trim((string) ($validated['search'] ?? ''));
        $view = config('public_ui.v2') ? 'public.clinics.index' : 'clinics.index';

        return view($view, [
            'areas' => Area::orderBy('name')->get(),
            'selectedAreaId' => $areaId,
            'specializations' => Specialization::orderBy('name')->get(),
            'selectedSpecializationId' => $specializationId,
            'searchTerm' => $searchTerm,
            'clinics' => Clinic::query()
                ->with([
                    'area',
                    'doctors.user',
                    'doctors.specialization',
                    'doctors.reviews' => fn ($query) => $query->where('review_type', Review::TYPE_DOCTOR),
                ])
                ->withCount('doctors')
                ->when($areaId, fn ($query) => $query->where('area_id', $areaId))
                ->when($searchTerm !== '', function ($query) use ($searchTerm): void {
                    $query->where(function ($searchQuery) use ($searchTerm): void {
                        $searchQuery->where('name', 'like', '%'.$searchTerm.'%')
                            ->orWhereHas('doctors.user', fn ($doctorQuery) => $doctorQuery->where('name', 'like', '%'.$searchTerm.'%'))
                            ->orWhereHas('doctors.specialization', fn ($specialtyQuery) => $specialtyQuery->where('name', 'like', '%'.$searchTerm.'%'));
                    });
                })
                ->when(
                    $specializationId,
                    fn ($query) => $query->whereHas('doctors', fn ($doctorQuery) => $doctorQuery->where('specialization_id', $specializationId))
                )
                ->where('is_active', true)
                ->orderBy('name')
                ->paginate(9)
                ->withQueryString(),
        ]);
    }

    public function show(Clinic $clinic): View
    {
        $patient = request()->user();
        $reviewableDoctorIds = [];
        $patientReviews = collect();

        if ($patient?->isPatient()) {
            $reviewableDoctorIds = $patient->patientAppointments()
                ->where('clinic_id', $clinic->id)
                ->pluck('doctor_id')
                ->unique()
                ->values()
                ->all();

            $patientReviews = Review::where('user_id', $patient->id)
                ->where('review_type', Review::TYPE_DOCTOR)
                ->whereIn('doctor_id', $reviewableDoctorIds ?: [0])
                ->get()
                ->keyBy('doctor_id');
        }

        return view('clinics.show', [
            'clinic' => $clinic->load([
                'area',
                'doctors' => fn ($query) => $query
                    ->withAvg(['reviews as average_rating' => fn ($reviewQuery) => $reviewQuery->where('review_type', Review::TYPE_DOCTOR)], 'rating')
                    ->withCount(['reviews as reviews_count' => fn ($reviewQuery) => $reviewQuery->where('review_type', Review::TYPE_DOCTOR)])
                    ->orderByDesc('average_rating')
                    ->orderBy('id'),
                'doctors.user',
                'doctors.specialization',
                'doctors.schedules' => fn ($query) => $query->where('is_active', true)->orderBy('weekday')->orderBy('starts_at'),
                'doctors.reviews' => fn ($query) => $query->with('user')->where('review_type', Review::TYPE_DOCTOR)->latest(),
            ]),
            'reviewableDoctorIds' => $reviewableDoctorIds,
            'patientReviews' => $patientReviews,
        ]);
    }
}
