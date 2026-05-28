<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    public const DEFAULT_KM_LIMIT = 3500;
    public const DEFAULT_EXTRA_KM_RATE = 5;

    protected $fillable = [
        'vehicle_name',
        'vehicle_number',
        'owner_name',
        'fixed_monthly_amount',
        'km_limit',
        'extra_km_rate',
        'ot_rate_per_hour',
        'tds_percent',
    ];

    public function monthlyLogs()
    {
        return $this->hasMany(VehicleMonthlyLog::class);
    }

    public function getDisplayNameAttribute()
    {
        return $this->vehicle_name . ' - ' . $this->vehicle_number;
    }
}
