<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Appointment;

class NotificationService
{
    public function createForAppointment(Appointment $appointment, int $userId, string $type, string $title, string $message, ?string $actionUrl = null): AppNotification
    {
        return AppNotification::firstOrCreate(
            [
                'user_id' => $userId,
                'appointment_id' => $appointment->id,
                'type' => $type,
            ],
            [
                'title' => $title,
                'message' => $message,
                'action_url' => $actionUrl ?? route('appointments.show', $appointment),
            ]
        );
    }
}
