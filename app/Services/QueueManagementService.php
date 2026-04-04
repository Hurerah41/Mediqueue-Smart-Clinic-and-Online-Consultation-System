<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\QueueToken;

class QueueManagementService
{
    public function refreshDoctorQueue(int $doctorId, string $queueDate): void
    {
        $waitingTokens = QueueToken::query()
            ->with(['appointment.doctor'])
            ->where('doctor_id', $doctorId)
            ->whereDate('queue_date', $queueDate)
            ->where('status', QueueToken::STATUS_WAITING)
            ->orderBy('token_number')
            ->get();

        foreach ($waitingTokens as $index => $token) {
            $token->appointment?->update([
                'estimated_wait_minutes' => $index * ($token->appointment->doctor->avg_consultation_minutes ?? 15),
            ]);
        }
    }

    public function queuePosition(Appointment $appointment): int
    {
        if (! $appointment->queueToken || $appointment->queueToken->status !== QueueToken::STATUS_WAITING) {
            return 0;
        }

        return QueueToken::query()
            ->where('doctor_id', $appointment->doctor_id)
            ->whereDate('queue_date', $appointment->appointment_date)
            ->where('status', QueueToken::STATUS_WAITING)
            ->where('token_number', '<', $appointment->queueToken->token_number)
            ->count();
    }
}
