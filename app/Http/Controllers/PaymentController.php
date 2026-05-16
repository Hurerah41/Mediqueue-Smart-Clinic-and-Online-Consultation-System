<?php

namespace App\Http\Controllers;

use App\Models\AppointmentPayment;
use App\Models\Appointment;
use App\Services\AppointmentPaymentService;
use App\Services\NotificationService;
use App\Services\QueueManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Request $request, AppointmentPayment $payment): View
    {
        $this->authorizePaymentAccess($request, $payment);

        return view('payments.checkout', [
            'payment' => $payment->load(['appointment.doctor.user', 'appointment.doctor.specialization', 'clinic', 'patient']),
            'gatewayLabel' => config('payments.gateways.'.config('payments.gateway').'.label', 'Payment Gateway'),
        ]);
    }

    public function confirm(
        Request $request,
        AppointmentPayment $payment,
        AppointmentPaymentService $paymentService,
        NotificationService $notificationService,
        QueueManagementService $queueService
    ): RedirectResponse {
        $this->authorizePaymentAccess($request, $payment);

        if ($payment->status === AppointmentPayment::STATUS_CANCELLED) {
            return redirect()->route('payments.show', $payment)->withErrors(['payment' => 'This payment was cancelled. Please book again.']);
        }

        $appointment = $paymentService->confirm($payment, $notificationService, $queueService);

        return redirect()->route('appointments.show', $appointment)->with('success', 'Payment confirmed. Your online appointment token is booked.');
    }

    public function cancel(Request $request, AppointmentPayment $payment): RedirectResponse
    {
        $this->authorizePaymentAccess($request, $payment);

        if ($payment->status === AppointmentPayment::STATUS_PENDING) {
            $payment->update(['status' => AppointmentPayment::STATUS_CANCELLED]);
            $payment->appointment()->update(['status' => Appointment::STATUS_CANCELLED]);
        }

        return redirect()->route('clinics.show', $payment->clinic->slug)->with('success', 'Payment cancelled. No token was generated.');
    }

    private function authorizePaymentAccess(Request $request, AppointmentPayment $payment): void
    {
        $user = $request->user();

        if ($user->isPatient()) {
            abort_unless($payment->patient_id === $user->id, 403);
        }

        if ($user->isAdmin()) {
            abort_unless($payment->clinic_id === $user->clinic_id, 403);
        }

        if ($user->isDoctor()) {
            abort_unless($user->doctorProfile?->id === $payment->doctor_id, 403);
        }
    }
}
