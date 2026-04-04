<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminDoctorScheduleController extends Controller
{
    public function store(Request $request, Doctor $doctor): RedirectResponse
    {
        abort_unless($request->user()->clinic_id === $doctor->clinic_id, 403);

        $validated = $request->validate([
            'weekday' => ['required', 'integer', 'between:0,6'],
            'starts_at' => ['required', 'date_format:H:i'],
            'ends_at' => ['required', 'date_format:H:i', 'after:starts_at'],
            'slot_limit' => ['required', 'integer', 'min:1', 'max:500'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        DoctorSchedule::updateOrCreate(
            [
                'doctor_id' => $doctor->id,
                'weekday' => $validated['weekday'],
            ],
            [
                'starts_at' => $validated['starts_at'],
                'ends_at' => $validated['ends_at'],
                'slot_limit' => $validated['slot_limit'],
                'is_active' => (bool) $validated['is_active'],
            ]
        );

        return back()->with('success', 'Doctor schedule saved successfully.');
    }

    public function destroy(Request $request, Doctor $doctor, DoctorSchedule $schedule): RedirectResponse
    {
        abort_unless($request->user()->clinic_id === $doctor->clinic_id, 403);
        abort_unless($schedule->doctor_id === $doctor->id, 403);

        $schedule->delete();

        return back()->with('success', 'Doctor schedule removed.');
    }
}
