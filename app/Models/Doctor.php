<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'user_id',
        'specialization_id',
        'license_no',
        'profile_photo_path',
        'experience_years',
        'consultation_fee',
        'offers_online_consultation',
        'avg_consultation_minutes',
        'bio',
    ];

    protected function casts(): array
    {
        return [
            'offers_online_consultation' => 'boolean',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function helpers(): HasMany
    {
        return $this->hasMany(User::class, 'doctor_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
