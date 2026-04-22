<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuperAdminClinicController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'new_area_name' => trim((string) $request->input('new_area_name')),
            'new_area_city' => trim((string) $request->input('new_area_city', 'Karachi')),
        ]);

        $validated = $request->validate([
            'area_id' => ['nullable', 'required_without:new_area_name', 'exists:areas,id'],
            'new_area_name' => ['nullable', 'required_without:area_id', 'string', 'max:120'],
            'new_area_city' => ['nullable', 'string', 'max:120'],
            'clinic_name' => ['required', 'string', 'max:255'],
            'clinic_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
            'opens_at' => ['nullable', 'date_format:H:i'],
            'closes_at' => ['nullable', 'date_format:H:i', 'after:opens_at'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_phone' => ['nullable', 'string', 'max:30'],
        ]);

        DB::transaction(function () use ($validated): void {
            $areaId = $validated['area_id'] ?? null;

            if (! $areaId) {
                $area = Area::firstOrCreate(
                    ['name' => $validated['new_area_name']],
                    ['city' => $validated['new_area_city'] ?: 'Karachi']
                );

                $areaId = $area->id;
            }

            $clinic = Clinic::create([
                'area_id' => $areaId,
                'name' => $validated['clinic_name'],
                'slug' => Str::slug($validated['clinic_name']).'-'.Str::lower(Str::random(5)),
                'phone' => $validated['clinic_phone'] ?? null,
                'address' => $validated['address'],
                'opens_at' => $validated['opens_at'] ?? null,
                'closes_at' => $validated['closes_at'] ?? null,
                'is_active' => true,
            ]);

            User::create([
                'clinic_id' => $clinic->id,
                'name' => $validated['admin_name'],
                'phone' => $validated['admin_phone'] ?? null,
                'email' => $validated['admin_email'],
                'role' => User::ROLE_ADMIN,
                'password' => 'Admin@12345',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]);
        });

        return back()->with('success', 'Clinic and clinic admin were created. Temporary admin password: Admin@12345');
    }
}
