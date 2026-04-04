<?php

namespace App\Http\Controllers;

use App\Models\PlatformSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OwnerPlatformSettingController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'platform_name' => ['required', 'string', 'max:255'],
            'support_email' => ['required', 'email', 'max:255'],
            'commission_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'queue_alert_threshold' => ['required', 'integer', 'min:1', 'max:20'],
            'clinic_verification_policy' => ['nullable', 'string', 'max:5000'],
            'owner_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        PlatformSetting::updateOrCreate(['id' => 1], $validated);

        return back()->with('success', 'Platform settings updated successfully.');
    }

    public function sendTestEmail(): RedirectResponse
    {
        $settings = PlatformSetting::firstOrCreate(['id' => 1], [
            'platform_name' => 'MediQueue',
            'support_email' => 'support@mediqueue.test',
            'commission_percent' => 10,
            'queue_alert_threshold' => 2,
        ]);

        Mail::raw(
            "This is a MediQueue SMTP test email.\n\nIf you received this message, Gmail SMTP is configured correctly.",
            fn ($message) => $message
                ->to($settings->support_email)
                ->subject('MediQueue SMTP Test Email')
        );

        return back()->with('success', 'Test email sent to '.$settings->support_email.'.');
    }
}
