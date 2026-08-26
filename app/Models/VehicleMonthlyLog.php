<?php

namespace App\Models;

use Carbon\CarbonPeriod;
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
        $salaryWorkingDays = max(1, $this->salaryWorkingDays());
        $driverTotals = $this->dailyEntries()
            ->reorder()
            ->selectRaw("COALESCE(NULLIF(driver_name, ''), 'Rohit') as driver_name")
            ->selectRaw("SUM(CASE WHEN attendance_status IS NULL OR attendance_status = 'present' THEN 1 ELSE 0 END) as present_days")
            ->selectRaw("SUM(CASE WHEN attendance_status = 'half_day' THEN 1 ELSE 0 END) as half_days")
            ->selectRaw("SUM(CASE WHEN attendance_status = 'absent' THEN 1 ELSE 0 END) as absent_days")
            ->selectRaw("SUM(CASE WHEN attendance_status IS NULL OR attendance_status IN ('present', 'half_day') THEN ot_minutes ELSE 0 END) as ot_minutes")
            ->selectRaw("SUM(CASE WHEN attendance_status IS NULL OR attendance_status = 'present' THEN 1 WHEN attendance_status = 'half_day' THEN 0.5 ELSE 0 END) as payable_days")
            ->groupBy('driver_name')
            ->get();

        if ($driverTotals->isEmpty()) {
            $driverTotals = collect([(object)[
                'driver_name' => 'Rohit',
                'present_days' => $salaryWorkingDays,
                'half_days' => 0,
                'absent_days' => 0,
                'ot_minutes' => (int)$this->total_ot_minutes,
                'payable_days' => $salaryWorkingDays,
            ]]);
        }

        $driverNames = $driverTotals->pluck('driver_name')->all();
        $this->driverPayments()->whereNotIn('driver_name', $driverNames)->delete();

        foreach ($driverTotals as $driverTotal) {
            $driverName = $driverTotal->driver_name ?: 'Rohit';
            $existingPayment = $this->driverPayments()
                ->where('driver_name', $driverName)
                ->first();
            $payableDays = (float)$driverTotal->payable_days;
            $otMinutes = (int)$driverTotal->ot_minutes;
            $otHours = $otMinutes / 60;
            $otRate = (float)($existingPayment->ot_rate_per_hour ?? VehicleDriverPayment::DEFAULT_OT_RATE_PER_HOUR);
            $otAmount = $otHours * $otRate;
            $monthlyPayment = (float)($existingPayment->monthly_payment ?? VehicleDriverPayment::DEFAULT_FIXED_PAYMENT);
            $perDayPayment = $monthlyPayment / $salaryWorkingDays;
            $payableFixedPayment = $perDayPayment * $payableDays;
            $roundedFixedPayment = round($payableFixedPayment, 2);
            $roundedOtAmount = round($otAmount, 2);
            $advancePayment = $existingPayment
                ? (float)$existingPayment->advances()->sum('amount')
                : 0;
            $totalPayment = round($roundedFixedPayment + $roundedOtAmount, 2);

            $this->driverPayments()->updateOrCreate(
                ['driver_name' => $driverName],
                [
                    'monthly_payment' => round($monthlyPayment, 2),
                    'fixed_payment' => $roundedFixedPayment,
                    'ot_minutes' => $otMinutes,
                    'ot_hours' => round($otHours, 2),
                    'ot_rate_per_hour' => $otRate,
                    'ot_amount' => $roundedOtAmount,
                    'advance_payment' => round($advancePayment, 2),
                    'total_payment' => $totalPayment,
                    'net_payment' => round($totalPayment - $advancePayment, 2),
                ]
            );
        }
    }

    public function salaryWorkingDays(): int
    {
        $fromDate = $this->from_date;
        $toDate = $this->to_date;

        if (!$fromDate || !$toDate) {
            return $this->dailyEntries()->count();
        }

        return collect(CarbonPeriod::create($fromDate, $toDate))->count();
    }

    public static function formatMinutes($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT);
    }
}
