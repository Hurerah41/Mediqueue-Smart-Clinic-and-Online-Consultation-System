<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\ClinicApplication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuperAdminClinicApplicationController extends Controller
{
    public function approve(Request $request, ClinicApplication $clinicApplication): RedirectResponse
    {
        abort_unless($clinicApplication->status === ClinicApplication::STATUS_PENDING, 422);

        $validated = $request->validate([
            'owner_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($clinicApplication, $validated): void {
            $clinic = Clinic::create([
                'area_id' => $clinicApplication->area_id,
                'name' => $clinicApplication->clinic_name,
                'slug' => Str::slug($clinicApplication->clinic_name).'-'.Str::lower(Str::random(5)),
                'phone' => $clinicApplication->clinic_phone,
                'address' => $clinicApplication->address,
                'logo_path' => $clinicApplication->logo_path,
                'opens_at' => $clinicApplication->opens_at?->format('H:i:s'),
                'closes_at' => $clinicApplication->closes_at?->format('H:i:s'),
                'is_active' => true,
            ]);

            User::create([
                'clinic_id' => $clinic->id,
                'name' => $clinicApplication->admin_name,
                'phone' => $clinicApplication->admin_phone,
                'email' => $clinicApplication->admin_email,
                'role' => User::ROLE_ADMIN,
                'password' => 'Admin@12345',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]);
            $clinicApplication->update([
                'status' => ClinicApplication::STATUS_APPROVED,
                'owner_notes' => $validated['owner_notes'] ?? 'Approved',
                'reviewed_at' => now(),
            ]);
        });

        return back()->with('success', 'Clinic approved and admin account created. Temporary admin password: Admin@12345');
    }

    public function reject(Request $request, ClinicApplication $clinicApplication): RedirectResponse
    {
        abort_unless($clinicApplication->status === ClinicApplication::STATUS_PENDING, 422);

        $validated = $request->validate([
            'owner_notes' => ['required', 'string', 'max:2000'],
        ]);

        $clinicApplication->update([
            'status' => ClinicApplication::STATUS_REJECTED,
            'owner_notes' => $validated['owner_notes'],
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Clinic application rejected with notes.');
    }
}
