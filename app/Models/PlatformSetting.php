<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform_name',
        'support_email',
        'commission_percent',
        'queue_alert_threshold',
        'clinic_verification_policy',
        'owner_notes',
    ];
}
