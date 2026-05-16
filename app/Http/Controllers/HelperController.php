<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\QueueToken;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\QueueManagementService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HelperController extends Controller
{
    public function index(Request $request): View
    {
        $helper = $request->user();
        abort_unless($helper->isHelper() && $helper->clinic_id, 403);

        return view('dashboards.helper', [
            'helper' => $helper->loadMissing(['clinic.area', 'assignedDoctor.user', 'assignedDoctor.specialization']),
            'appointments' => $this->appointmentsQuery($helper)
                ->orderByRaw("CASE WHEN status = ? THEN 0 WHEN status = ? THEN 1 ELSE 2 END", [
                    Appointment::STATUS_IN_PROGRESS,
                    Appointment::STATUS_BOOKED,
                ])
                ->orderBy('appointment_time')
                ->orderBy('id')
                ->get(),
            'prescriptions' => $this->prescriptionsQuery($helper)
                ->latest('issued_at')
                ->take(12)
                ->get(),
            'stats' => [
                'waiting' => $this->queueTokensQuery($helper)->where('status', QueueToken::STATUS_WAITING)->count(),
                'serving' => $this->queueTokensQuery($helper)->where('status', QueueToken::STATUS_CALLED)->count(),
                'completed' => $this->queueTokensQuery($helper)->where('status', QueueToken::STATUS_COMPLETED)->count(),
            ],
        ]);
    }

    public function nextPatient(Request $request, QueueManagementService $queueService, NotificationService $notificationService): RedirectResponse
    {
        $helper = $request->user();
        abort_unless($helper->isHelper() && $helper->clinic_id, 403);

        $activeToken = $this->queueTokensQuery($helper)
            ->where('status', QueueToken::STATUS_CALLED)
            ->with('appointment')
            ->first();

        if ($activeToken) {
            return back()->withErrors(['queue' => 'A patient is already being served. Mark that patient completed before calling the next one.']);
        }

        $nextToken = $this->nextWaitingTokens($helper)->first();

        if (! $nextToken?->appointment) {
            return back()->withErrors(['queue' => 'No waiting patient is available in the queue right now.']);
        }

        $appointment = $nextToken->appointment;

        $appointment->update(['status' => Appointment::STATUS_IN_PROGRESS]);
        $nextToken->update([
            'status' => QueueToken::STATUS_CALLED,
            'called_at' => now(),
        ]);

        $queueService->refreshDoctorQueue($appointment->doctor_id, $appointment->appointment_date->toDateString());
        $doctorName = $appointment->doctor?->user?->name ? 'Dr. '.$appointment->doctor->user->name : 'the clinic team';

        $notificationService->createForAppointment(
            $appointment,
            $appointment->patient_id,
            AppNotification::TYPE_PATIENT_CALLED,
            'Please proceed for consultation',
            'Token #'.$nextToken->token_number.' has been called by '.$doctorName.'. Please proceed now.'
        );

        return back()->with('success', 'Next patient called successfully.');
    }

    public function markComplete(Request $request, Appointment $appointment, QueueManagementService $queueService, NotificationService $notificationService): RedirectResponse
    {
        $helper = $request->user();
        abort_unless($helper->isHelper() && $helper->clinic_id, 403);

        $appointment->loadMissing(['queueToken', 'doctor.user', 'prescription']);

        abort_unless($this->appointmentBelongsToHelperScope($appointment, $helper), 403);

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
        $doctorName = $appointment->doctor?->user?->name ? 'Dr. '.$appointment->doctor->user->name : 'the clinic team';

        $message = 'Your visit with '.$doctorName.' has been marked completed.';
        if ($appointment->prescription) {
            $message .= ' Your prescription is available on the dashboard.';
        }

        $notificationService->createForAppointment(
            $appointment,
            $appointment->patient_id,
            AppNotification::TYPE_CONSULTATION_COMPLETED,
            'Visit completed',
            $message
        );

        return back()->with('success', 'Patient marked as completed.');
    }

    private function appointmentsQuery(User $helper): Builder
    {
        return Appointment::query()
            ->with([
                'patient',
                'doctor.user',
                'doctor.specialization',
                'queueToken',
                'prescription.items',
            ])
            ->where('clinic_id', $helper->clinic_id)
            ->whereDate('appointment_date', now()->toDateString())
            ->when($helper->doctor_id, fn (Builder $query) => $query->where('doctor_id', $helper->doctor_id));
    }

    private function queueTokensQuery(User $helper): Builder
    {
        return QueueToken::query()
            ->where('clinic_id', $helper->clinic_id)
            ->whereDate('queue_date', now()->toDateString())
            ->when($helper->doctor_id, fn (Builder $query) => $query->where('doctor_id', $helper->doctor_id));
    }

    private function nextWaitingTokens(User $helper): Collection
    {
        return $this->queueTokensQuery($helper)
            ->with(['appointment.patient', 'appointment.doctor.user'])
            ->where('status', QueueToken::STATUS_WAITING)
            ->get()
            ->sortBy(fn (QueueToken $token) => sprintf(
                '%s-%06d',
                (string) ($token->appointment?->appointment_time ?? '23:59'),
                $token->token_number
            ))
            ->values();
    }

    private function prescriptionsQuery(User $helper): Builder
    {
        return Prescription::query()
            ->with(['patient', 'doctor.user', 'items', 'appointment.queueToken'])
            ->whereHas('appointment', function (Builder $query) use ($helper): void {
                $query->where('clinic_id', $helper->clinic_id)
                    ->whereDate('appointment_date', now()->toDateString());

                if ($helper->doctor_id) {
                    $query->where('doctor_id', $helper->doctor_id);
                }
            });
    }

    private function appointmentBelongsToHelperScope(Appointment $appointment, User $helper): bool
    {
        if ($appointment->clinic_id !== $helper->clinic_id) {
            return false;
        }

        if ($helper->doctor_id && $appointment->doctor_id !== $helper->doctor_id) {
            return false;
        }

        return $appointment->appointment_date?->isToday() ?? false;
    }
}
