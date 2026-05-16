<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Appointment;
use App\Models\AppointmentPayment;
use App\Models\Consultation;
use App\Models\QueueToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppointmentPaymentService
{
    public function confirm(AppointmentPayment $payment, NotificationService $notificationService, QueueManagementService $queueService): Appointment
    {
        return DB::transaction(function () use ($payment, $notificationService, $queueService): Appointment {
            $payment = AppointmentPayment::query()
                ->with(['appointment.doctor.user', 'appointment.queueToken'])
                ->lockForUpdate()
                ->findOrFail($payment->id);

            if ($payment->status === AppointmentPayment::STATUS_PAID) {
                return $payment->appointment;
            }

            $appointment = $payment->appointment;
            $appointmentDate = $appointment->appointment_date->toDateString();

            $tokenNumber = QueueToken::query()
                ->where('doctor_id', $appointment->doctor_id)
                ->whereDate('queue_date', $appointmentDate)
                ->lockForUpdate()
                ->max('token_number');

            $tokenNumber = ((int) $tokenNumber) + 1;

            $appointment->update([
                'status' => Appointment::STATUS_BOOKED,
                'estimated_wait_minutes' => max(0, ($tokenNumber - 1) * ($appointment->doctor->avg_consultation_minutes ?? 15)),
            ]);

            QueueToken::firstOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'clinic_id' => $appointment->clinic_id,
                    'doctor_id' => $appointment->doctor_id,
                    'queue_date' => $appointmentDate,
                    'token_number' => $tokenNumber,
                    'status' => QueueToken::STATUS_WAITING,
                ]
            );

            Consultation::firstOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'doctor_id' => $appointment->doctor_id,
                    'patient_id' => $appointment->patient_id,
                    'mode' => 'video',
                    'status' => 'waiting',
                    'video_room_url' => 'https://meet.jit.si/smart-clinic-'.$appointment->id,
                ]
            );

            $payment->update([
                'status' => AppointmentPayment::STATUS_PAID,
                'provider_reference' => $payment->provider_reference ?: 'MQ-'.Str::upper(Str::random(12)),
                'paid_at' => now(),
            ]);

            $queueService->refreshDoctorQueue($appointment->doctor_id, $appointmentDate);

            $appointment->load(['queueToken', 'doctor.user']);
            $notificationService->createForAppointment(
                $appointment,
                $appointment->patient_id,
                AppNotification::TYPE_TOKEN_BOOKED,
                'Payment received and token booked',
                'Your online consultation payment was received. Token #'.$appointment->queueToken?->token_number.' is confirmed for Dr. '.$appointment->doctor->user?->name.'.'
            );

            return $appointment;
        });
    }
}
