<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function storeDoctorReview(Request $request, Doctor $doctor): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->role === User::ROLE_PATIENT, 403);

        $hasVisitedDoctor = Appointment::where('doctor_id', $doctor->id)
            ->where('patient_id', $user->id)
            ->exists();

        if (! $hasVisitedDoctor) {
            return back()->with('error', 'Please book this doctor first before submitting a review.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'doctor_id' => $doctor->id,
                'review_type' => Review::TYPE_DOCTOR,
            ],
            $validated
        );

        return back()->with('success', 'Thanks for reviewing Dr. '.$doctor->user->name.'.');
    }

    public function storePlatformReview(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->role === User::ROLE_PATIENT, 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'doctor_id' => null,
                'review_type' => Review::TYPE_PLATFORM,
            ],
            $validated
        );

        return back()->with('success', 'Thanks for sharing your MediQueue feedback.');
    }
}
