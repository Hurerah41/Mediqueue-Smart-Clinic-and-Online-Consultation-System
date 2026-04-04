<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDoctorController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $admin = $request->user();
        abort_unless($admin->clinic_id, 422, 'Admin must belong to a clinic.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'specialization_id' => ['required', 'exists:specializations,id'],
            'license_no' => ['required', 'string', 'max:255', 'unique:doctors,license_no'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'experience_years' => ['required', 'integer', 'min:1', 'max:60'],
            'consultation_fee' => ['required', 'integer', 'min:0', 'max:50000'],
            'offers_online_consultation' => ['nullable', 'boolean'],
            'avg_consultation_minutes' => ['required', 'integer', 'min:5', 'max:180'],
            'bio' => ['nullable', 'string', 'max:4000'],
        ]);

        $photoPath = $request->hasFile('profile_photo')
            ? $request->file('profile_photo')->store('doctor-photos', 'public')
            : null;

        DB::transaction(function () use ($validated, $admin, $photoPath): void {
            $user = User::create([
                'clinic_id' => $admin->clinic_id,
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'],
                'role' => User::ROLE_DOCTOR,
                'password' => 'Doctor@12345',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]);

            Doctor::create([
                'clinic_id' => $admin->clinic_id,
                'user_id' => $user->id,
                'specialization_id' => $validated['specialization_id'],
                'license_no' => $validated['license_no'],
                'profile_photo_path' => $photoPath,
                'experience_years' => $validated['experience_years'],
                'consultation_fee' => $validated['consultation_fee'],
                'offers_online_consultation' => $validated['offers_online_consultation'] ?? false,
                'avg_consultation_minutes' => $validated['avg_consultation_minutes'],
                'bio' => $validated['bio'] ?? null,
            ]);
        });

        return back()->with('success', 'Doctor onboarded. Temporary password: Doctor@12345');
    }
}
