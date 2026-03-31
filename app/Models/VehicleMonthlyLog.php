<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleMonthlyLog extends Model
{
    protected $fillable = [
        'vehicle_id',
        'from_date',
        'to_date',
        'opening_reading',
        'closing_reading',
        'total_km',
        'diesel_total',
        'average_kmpl',
        'total_ot_minutes',
        'total_ot_hours',
        'total_ot_amount',
        'fixed_monthly_amount',
        'total_billing_amount',
        'tds_percent',
        'tds_amount',
        'net_payable',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function dailyEntries()
    {
        return $this->hasMany(VehicleDailyEntry::class)->orderBy('entry_date');
    }

    public function getFormattedOtAttribute()
    {
        return self::formatMinutes($this->total_ot_minutes);
    }

    public static function formatMinutes($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT);
    }
}