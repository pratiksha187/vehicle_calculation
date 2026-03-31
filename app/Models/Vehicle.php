<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'vehicle_name',
        'vehicle_number',
        'owner_name',
        'fixed_monthly_amount',
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