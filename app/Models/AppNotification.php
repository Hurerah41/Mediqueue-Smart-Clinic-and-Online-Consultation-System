<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    use HasFactory;

    public const TYPE_TOKEN_BOOKED = 'token_booked';
    public const TYPE_TURN_NEAR = 'turn_near';
    public const TYPE_PATIENT_CALLED = 'patient_called';
    public const TYPE_CONSULTATION_COMPLETED = 'consultation_completed';
    public const TYPE_PRESCRIPTION_READY = 'prescription_ready';

    protected $fillable = [
        'user_id',
        'appointment_id',
        'type',
        'title',
        'message',
        'action_url',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
