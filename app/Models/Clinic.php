<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_id',
        'name',
        'slug',
        'phone',
        'address',
        'logo_path',
        'brand_tagline',
        'brand_primary_color',
        'brand_secondary_color',
        'opens_at',
        'closes_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'opens_at' => 'datetime:H:i',
            'closes_at' => 'datetime:H:i',
            'is_active' => 'boolean',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hoursLabel(): string
    {
        if (! $this->opens_at || ! $this->closes_at) {
            return 'Hours not listed';
        }

        return $this->opens_at->format('g:i A').' - '.$this->closes_at->format('g:i A');
    }

    public function isOpenAt(?Carbon $dateTime = null): bool
    {
        if (! $this->opens_at || ! $this->closes_at) {
            return true;
        }

        $dateTime ??= now();
        $openAt = $dateTime->copy()->setTimeFromTimeString($this->opens_at->format('H:i:s'));
        $closeAt = $dateTime->copy()->setTimeFromTimeString($this->closes_at->format('H:i:s'));

        if ($closeAt->lessThanOrEqualTo($openAt)) {
            $closeAt->addDay();

            if ($dateTime->lt($openAt)) {
                $openAt->subDay();
            }
        }

        return $dateTime->betweenIncluded($openAt, $closeAt);
    }

    public function nextAvailabilityLabel(?Carbon $dateTime = null): string
    {
        if (! $this->opens_at || ! $this->closes_at) {
            return 'Available now';
        }

        $dateTime ??= now();

        if ($this->isOpenAt($dateTime)) {
            $minutes = max(10, min(35, 10 + ($this->id % 6) * 5));

            return "In {$minutes} mins";
        }

        $openAt = $dateTime->copy()->setTimeFromTimeString($this->opens_at->format('H:i:s'));

        if ($dateTime->greaterThanOrEqualTo($openAt)) {
            $openAt->addDay();
        }

        return ($openAt->isTomorrow() ? 'Tomorrow, ' : 'Today, ').$openAt->format('g A');
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(ClinicSubscription::class)->latestOfMany();
    }
}
