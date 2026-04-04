<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\ClinicApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicApplicationController extends Controller
{
    public function create(): View
    {
        return view('clinic-applications.create', [
            'areas' => Area::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'area_id' => ['required', 'exists:areas,id'],
            'clinic_name' => ['required', 'string', 'max:255'],
            'clinic_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
            'opens_at' => ['nullable', 'date_format:H:i'],
            'closes_at' => ['nullable', 'date_format:H:i', 'after:opens_at'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255'],
            'admin_phone' => ['nullable', 'string', 'max:30'],
            'clinic_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'license_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $documentPath = $request->file('license_document')
            ? $request->file('license_document')->store('clinic-applications', 'public')
            : null;

        $logoPath = $request->file('clinic_logo')
            ? $request->file('clinic_logo')->store('clinic-logos', 'public')
            : null;

        ClinicApplication::create([
            ...$validated,
            'license_document_path' => $documentPath,
            'logo_path' => $logoPath,
            'status' => ClinicApplication::STATUS_PENDING,
        ]);

        return redirect()
            ->route('clinic-applications.create')
            ->with('success', 'Application submitted successfully. Owner will review and approve your clinic.');
    }
}
