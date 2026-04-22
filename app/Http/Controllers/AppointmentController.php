<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\PlatformSetting;
use App\Models\QueueToken;
use App\Services\NotificationService;
use App\Services\QueueManagementService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function store(Request $request, QueueManagementService $queueService, NotificationService $notificationService): RedirectResponse
    {
        $validated = $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['nullable', 'date_format:H:i'],
            'consultation_type' => ['required', Rule::in(['physical', 'online'])],
            'symptoms' => ['nullable', 'string', 'max:2000'],
        ]);

        $doctor = Doctor::with(['clinic', 'user'])->findOrFail($validated['doctor_id']);
        $appointmentDate = Carbon::parse($validated['appointment_date']);
        $clinicHoursLabel = $doctor->clinic->hoursLabel();
        $schedule = DoctorSchedule::query()
            ->where('doctor_id', $doctor->id)
            ->where('weekday', $appointmentDate->dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (! $schedule) {
            return back()->withErrors(['appointment_date' => 'Doctor is not available on the selected date.'])->withInput();
        }

        if (! empty($validated['appointment_time'])) {
            $requestedDateTime = Carbon::parse($appointmentDate->toDateString().' '.$validated['appointment_time']);
            $requestedTime = $requestedDateTime->format('H:i:s');

            if ($requestedTime < $schedule->starts_at->format('H:i:s') || $requestedTime > $schedule->ends_at->format('H:i:s')) {
                return back()->withErrors(['appointment_time' => 'Selected time is outside doctor schedule.'])->withInput();
            }

            if (! $doctor->clinic->isOpenAt($requestedDateTime)) {
                return back()->withErrors(['appointment_time' => 'Selected time is outside clinic hours. Clinic hours: '.$clinicHoursLabel.'.'])->withInput();
            }
        } elseif ($appointmentDate->isToday() && ! $doctor->clinic->isOpenAt(now())) {
            return back()->withErrors(['appointment_date' => 'This clinic is closed right now. Tokens can be booked during clinic hours: '.$clinicHoursLabel.'.'])->withInput();
        }

        $bookedSlots = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $appointmentDate->toDateString())
            ->count();

        if ($bookedSlots >= $schedule->slot_limit) {
            return back()->withErrors(['appointment_date' => 'All slots for this doctor are booked on the selected date.'])->withInput();
        }

        if ($validated['consultation_type'] === 'online' && ! $doctor->offers_online_consultation) {
            return back()->withErrors(['consultation_type' => 'This doctor does not offer online consultation.'])->withInput();
        }

        $appointment = DB::transaction(function () use ($validated, $doctor, $request): Appointment {
            $tokenNumber = QueueToken::query()
                ->where('doctor_id', $doctor->id)
                ->whereDate('queue_date', $validated['appointment_date'])
                ->max('token_number');

            $tokenNumber = ((int) $tokenNumber) + 1;

            $appointment = Appointment::create([
                'clinic_id' => $doctor->clinic_id,
                'doctor_id' => $doctor->id,
                'patient_id' => $request->user()->id,
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'consultation_type' => $validated['consultation_type'],
                'symptoms' => $validated['symptoms'] ?? null,
                'status' => Appointment::STATUS_BOOKED,
                'estimated_wait_minutes' => max(0, ($tokenNumber - 1) * $doctor->avg_consultation_minutes),
            ]);

            QueueToken::create([
                'appointment_id' => $appointment->id,
                'clinic_id' => $doctor->clinic_id,
                'doctor_id' => $doctor->id,
                'queue_date' => $validated['appointment_date'],
                'token_number' => $tokenNumber,
                'status' => QueueToken::STATUS_WAITING,
            ]);

            if ($validated['consultation_type'] === 'online') {
                Consultation::create([
                    'appointment_id' => $appointment->id,
                    'doctor_id' => $doctor->id,
                    'patient_id' => $request->user()->id,
                    'mode' => 'video',
                    'status' => 'waiting',
                    'video_room_url' => 'https://meet.jit.si/smart-clinic-'.$appointment->id,
                ]);
            }

            return $appointment;
        });

        $queueService->refreshDoctorQueue($appointment->doctor_id, $appointment->appointment_date->toDateString());
        $notificationService->createForAppointment(
            $appointment,
            $appointment->patient_id,
            AppNotification::TYPE_TOKEN_BOOKED,
            'Token booked successfully',
            'Your queue token #'.$appointment->queueToken?->token_number.' has been generated for Dr. '.$doctor->user?->name.'.'
        );

        return redirect()->route('appointments.show', $appointment)->with('success', 'Appointment booked and token generated.');
    }

    public function show(Request $request, Appointment $appointment, QueueManagementService $queueService): View
    {
        $this->authorizeAppointmentAccess($request, $appointment);

        return view('appointments.show', [
            'appointment' => $appointment->load(['clinic.area', 'doctor.user', 'doctor.specialization', 'patient', 'queueToken', 'consultation.messages.sender', 'prescription.items']),
            'queuePosition' => $queueService->queuePosition($appointment),
        ]);
    }

    public function status(Request $request, Appointment $appointment, QueueManagementService $queueService, NotificationService $notificationService): JsonResponse
    {
        $this->authorizeAppointmentAccess($request, $appointment);

        $appointment->load(['queueToken', 'doctor.user']);
        $queuePosition = $queueService->queuePosition($appointment);
        $estimatedWaitMinutes = $queuePosition * ($appointment->doctor->avg_consultation_minutes ?? 15);
        $queueAlertThreshold = PlatformSetting::query()->value('queue_alert_threshold') ?? 2;
        $currentlyServingToken = QueueToken::query()
            ->where('doctor_id', $appointment->doctor_id)
            ->whereDate('queue_date', $appointment->appointment_date->toDateString())
            ->where('status', QueueToken::STATUS_CALLED)
            ->orderBy('token_number')
            ->value('token_number');

        $progressPercent = match ($appointment->queueToken?->status) {
            QueueToken::STATUS_COMPLETED => 100,
            QueueToken::STATUS_CALLED => 92,
            QueueToken::STATUS_WAITING => max(12, min(88, 88 - ($queuePosition * 18))),
            default => 10,
        };

        if (
            $request->user()->isPatient()
            && $appointment->queueToken?->status === QueueToken::STATUS_WAITING
            && $queuePosition <= $queueAlertThreshold
        ) {
            $notificationService->createForAppointment(
                $appointment,
                $appointment->patient_id,
                AppNotification::TYPE_TURN_NEAR,
                'Your turn is near',
                'Only '.$queuePosition.' patient(s) ahead of token #'.$appointment->queueToken->token_number.' for Dr. '.$appointment->doctor->user->name.'.'
            );
        }

        return response()->json([
            'token_number' => $appointment->queueToken?->token_number,
            'token_status' => $appointment->queueToken?->status,
            'appointment_status' => $appointment->status,
            'queue_position' => $queuePosition,
            'currently_serving_token' => $currentlyServingToken,
            'progress_percent' => $progressPercent,
            'estimated_wait_minutes' => $estimatedWaitMinutes,
            'eta_seconds' => max(0, $estimatedWaitMinutes * 60),
        ]);
    }

    private function authorizeAppointmentAccess(Request $request, Appointment $appointment): void
    {
        $user = $request->user();

        if ($user->isPatient()) {
            abort_unless($appointment->patient_id === $user->id, 403);
        }

        if ($user->isDoctor()) {
            abort_unless($user->doctorProfile?->id === $appointment->doctor_id, 403);
        }

        if ($user->isAdmin()) {
            abort_unless($appointment->clinic_id === $user->clinic_id, 403);
        }
    }

}
