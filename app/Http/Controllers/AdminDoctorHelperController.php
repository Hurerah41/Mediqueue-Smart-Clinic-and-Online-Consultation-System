<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminDoctorHelperController extends Controller
{
    public function update(Request $request, Doctor $doctor): RedirectResponse
    {
        $admin = $request->user();
        abort_unless($admin->clinic_id && $doctor->clinic_id === $admin->clinic_id, 403);

        $validated = $request->validate([
            'helper_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('role', User::ROLE_HELPER)
                    ->where('clinic_id', $admin->clinic_id)
                ),
            ],
        ]);

        $helperId = $validated['helper_id'] ?? null;

        if ($helperId) {
            User::whereKey($helperId)->update(['doctor_id' => $doctor->id]);

            return back()->with('success', 'Helper assigned to Dr. '.$doctor->user->name.'.');
        }

        User::query()
            ->where('role', User::ROLE_HELPER)
            ->where('clinic_id', $admin->clinic_id)
            ->where('doctor_id', $doctor->id)
            ->update(['doctor_id' => null]);

        return back()->with('success', 'Helpers unassigned from Dr. '.$doctor->user->name.'.');
    }
}
