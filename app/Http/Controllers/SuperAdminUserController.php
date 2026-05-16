<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuperAdminUserController extends Controller
{
    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403, 'Owner account cannot be modified here.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_DOCTOR, User::ROLE_HELPER, User::ROLE_PATIENT])],
            'clinic_id' => ['nullable', 'exists:clinics,id'],
            'doctor_id' => ['nullable', 'exists:doctors,id'],
        ]);

        if (in_array($validated['role'], [User::ROLE_ADMIN, User::ROLE_DOCTOR, User::ROLE_HELPER], true) && empty($validated['clinic_id'])) {
            return back()->withErrors(['clinic_id' => 'Clinic must be selected for clinic admins, helpers, and doctors.']);
        }

        if ($user->doctorProfile && $validated['role'] !== User::ROLE_DOCTOR) {
            return back()->withErrors(['role' => 'This user has a doctor profile and must remain a doctor.']);
        }

        if (! $user->doctorProfile && $validated['role'] === User::ROLE_DOCTOR) {
            return back()->withErrors(['role' => 'Create doctors from the clinic admin dashboard so a doctor profile is generated.']);
        }

        $doctorId = null;
        if ($validated['role'] === User::ROLE_HELPER && ! empty($validated['doctor_id'])) {
            $doctor = Doctor::find($validated['doctor_id']);

            if (! $doctor || (int) $doctor->clinic_id !== (int) $validated['clinic_id']) {
                return back()->withErrors(['doctor_id' => 'Assigned doctor must belong to the selected clinic.']);
            }

            $doctorId = $doctor->id;
        }

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'clinic_id' => $validated['role'] === User::ROLE_PATIENT ? null : $validated['clinic_id'],
            'doctor_id' => $validated['role'] === User::ROLE_HELPER ? $doctorId : null,
        ]);

        return back()->with('success', 'User updated successfully.');
    }
}
