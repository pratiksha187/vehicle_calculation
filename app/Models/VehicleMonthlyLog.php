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

    public function driverPayment()
    {
        return $this->hasOne(VehicleDriverPayment::class);
    }

    public function driverPayments()
    {
        return $this->hasMany(VehicleDriverPayment::class);
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
        $tdsPercent = Vehicle::DEFAULT_TDS_PERCENT;
        $tdsAmount = ($totalBilling * $tdsPercent) / 100;

        $this->update([
            'km_limit' => $kmLimit,
            'extra_km_rate' => round($extraKmRate, 2),
            'extra_km' => $extraKm,
            'extra_km_amount' => round($extraKmAmount, 2),
            'total_billing_amount' => round($totalBilling, 2),
            'tds_percent' => $tdsPercent,
            'tds_amount' => round($tdsAmount, 2),
            'net_payable' => round($totalBilling - $tdsAmount, 2),
        ]);

        $this->syncDriverPaymentFromSavedTotals();
    }

    public function syncDriverPaymentFromSavedTotals(): void
    {
        $fixedPayment = VehicleDriverPayment::DEFAULT_FIXED_PAYMENT;
        $otRate = VehicleDriverPayment::DEFAULT_OT_RATE_PER_HOUR;
        $driverTotals = $this->dailyEntries()
            ->reorder()
            ->selectRaw("COALESCE(NULLIF(driver_name, ''), 'Driver 1') as driver_name")
            ->selectRaw("SUM(CASE WHEN attendance_status IS NULL OR attendance_status = 'present' THEN 1 ELSE 0 END) as present_days")
            ->selectRaw("SUM(CASE WHEN attendance_status = 'absent' THEN 1 ELSE 0 END) as absent_days")
            ->selectRaw("SUM(CASE WHEN attendance_status IS NULL OR attendance_status = 'present' THEN ot_minutes ELSE 0 END) as ot_minutes")
            ->groupBy('driver_name')
            ->get();

        if ($driverTotals->isEmpty()) {
            $driverTotals = collect([(object)[
                'driver_name' => 'Driver 1',
                'ot_minutes' => (int)$this->total_ot_minutes,
            ]]);
        }

        $driverNames = $driverTotals->pluck('driver_name')->all();
        $this->driverPayments()->whereNotIn('driver_name', $driverNames)->delete();

        foreach ($driverTotals as $driverTotal) {
            $otMinutes = (int)$driverTotal->ot_minutes;
            $otHours = $otMinutes / 60;
            $otAmount = $otHours * $otRate;

            $this->driverPayments()->updateOrCreate(
                ['driver_name' => $driverTotal->driver_name ?: 'Driver 1'],
                [
                    'fixed_payment' => $fixedPayment,
                    'ot_minutes' => $otMinutes,
                    'ot_hours' => round($otHours, 2),
                    'ot_rate_per_hour' => $otRate,
                    'ot_amount' => round($otAmount, 2),
                    'total_payment' => round($fixedPayment + $otAmount, 2),
                ]
            );
        }
    }

    public static function formatMinutes($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT);
    }
}
