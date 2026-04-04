<?php

namespace App\Http\Controllers;

use App\Models\ClinicSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminSubscriptionController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $clinic = $request->user()->clinic;
        abort_unless($clinic, 422, 'Admin must belong to a clinic.');

        $validated = $request->validate([
            'plan_name' => ['required', 'string', 'max:255'],
            'monthly_fee' => ['required', 'integer', 'min:0', 'max:1000000'],
            'doctor_limit' => ['required', 'integer', 'min:1', 'max:1000'],
            'monthly_appointment_limit' => ['required', 'integer', 'min:1', 'max:1000000'],
            'status' => ['required', Rule::in([
                ClinicSubscription::STATUS_ACTIVE,
                ClinicSubscription::STATUS_PAST_DUE,
                ClinicSubscription::STATUS_CANCELLED,
            ])],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
        ]);

        ClinicSubscription::updateOrCreate(
            ['clinic_id' => $clinic->id],
            [
                ...$validated,
                'last_billed_at' => now(),
            ]
        );

        return back()->with('success', 'Subscription plan updated successfully.');
    }
}
