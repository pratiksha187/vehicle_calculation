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
        'km_limit',
        'extra_km_rate',
        'extra_km',
        'extra_km_amount',
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

    public function syncBillingTotalsFromSavedTotals(): void
    {
        $kmLimit = (int)($this->km_limit ?? Vehicle::DEFAULT_KM_LIMIT);
        $extraKmRate = (float)($this->extra_km_rate ?? Vehicle::DEFAULT_EXTRA_KM_RATE);
        $extraKm = max(0, (int)$this->total_km - $kmLimit);
        $extraKmAmount = $extraKm * $extraKmRate;
        $totalBilling = (float)$this->fixed_monthly_amount + (float)$this->total_ot_amount + $extraKmAmount;
        $tdsAmount = ($totalBilling * (float)$this->tds_percent) / 100;

        $this->update([
            'km_limit' => $kmLimit,
            'extra_km_rate' => round($extraKmRate, 2),
            'extra_km' => $extraKm,
            'extra_km_amount' => round($extraKmAmount, 2),
            'total_billing_amount' => round($totalBilling, 2),
            'tds_amount' => round($tdsAmount, 2),
            'net_payable' => round($totalBilling - $tdsAmount, 2),
        ]);
    }

    public static function formatMinutes($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT);
    }
}
