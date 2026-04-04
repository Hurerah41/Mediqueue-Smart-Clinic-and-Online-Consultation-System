<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Appointment;
use App\Models\QueueToken;
use App\Services\NotificationService;
use App\Services\QueueManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DoctorQueueController extends Controller
{
    public function liveAppointments(Request $request): JsonResponse
    {
        $doctor = $request->user()->doctorProfile;
        abort_unless($doctor, 403);

        $appointments = Appointment::query()
            ->with(['patient', 'queueToken', 'consultation'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', now()->toDateString())
            ->orderBy('id')
            ->get()
            ->map(fn (Appointment $appointment) => [
                'id' => $appointment->id,
                'patient_name' => $appointment->patient->name,
                'consultation_type' => ucfirst($appointment->consultation_type),
                'token_number' => $appointment->queueToken?->token_number,
                'token_status' => $appointment->queueToken?->status,
                'appointment_status' => $appointment->status,
                'symptoms' => $appointment->symptoms ?: 'No symptoms note provided.',
                'consultation_url' => $appointment->consultation ? route('consultations.show', $appointment->consultation) : null,
                'call_url' => route('doctor.appointments.call', $appointment),
                'complete_url' => route('doctor.appointments.complete', $appointment),
            ]);

        return response()->json(['appointments' => $appointments]);
    }

    public function callPatient(Request $request, Appointment $appointment, QueueManagementService $queueService, NotificationService $notificationService): RedirectResponse|JsonResponse
    {
        $doctor = $request->user()->doctorProfile;
        abort_unless($doctor && $appointment->doctor_id === $doctor->id, 403);

        $appointment->update(['status' => Appointment::STATUS_IN_PROGRESS]);
        $appointment->queueToken?->update([
            'status' => QueueToken::STATUS_CALLED,
            'called_at' => now(),
        ]);
        $appointment->consultation?->update([
            'status' => 'active',
            'started_at' => now(),
        ]);

        $queueService->refreshDoctorQueue($appointment->doctor_id, $appointment->appointment_date->toDateString());
        $notificationService->createForAppointment(
            $appointment,
            $appointment->patient_id,
            AppNotification::TYPE_PATIENT_CALLED,
            'Doctor is calling you now',
            'Token #'.$appointment->queueToken?->token_number.' has been called by Dr. '.$doctor->user->name.'. Please proceed now.'
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Next patient called.',
                'appointment_status' => $appointment->fresh()->status,
                'token_status' => $appointment->queueToken?->fresh()?->status,
            ]);
        }

        return back()->with('success', 'Next patient called.');
    }

    public function complete(Request $request, Appointment $appointment, QueueManagementService $queueService, NotificationService $notificationService): RedirectResponse|JsonResponse
    {
        $doctor = $request->user()->doctorProfile;
        abort_unless($doctor && $appointment->doctor_id === $doctor->id, 403);

        $appointment->update(['status' => Appointment::STATUS_COMPLETED]);
        $appointment->queueToken?->update([
            'status' => QueueToken::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
        $appointment->consultation?->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        $queueService->refreshDoctorQueue($appointment->doctor_id, $appointment->appointment_date->toDateString());
        $notificationService->createForAppointment(
            $appointment,
            $appointment->patient_id,
            AppNotification::TYPE_CONSULTATION_COMPLETED,
            'Consultation completed',
            'Your consultation with Dr. '.$doctor->user->name.' has been marked as completed.'
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Appointment marked as completed.',
                'appointment_status' => $appointment->fresh()->status,
                'token_status' => $appointment->queueToken?->fresh()?->status,
            ]);
        }

        return back()->with('success', 'Appointment marked as completed.');
    }
}
