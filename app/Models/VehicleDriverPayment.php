<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleDriverPayment extends Model
{
    public const DEFAULT_FIXED_PAYMENT = 20000;
    public const DEFAULT_OT_CALCULATION_AMOUNT = 18000;
    public const DEFAULT_OT_DAYS_PER_MONTH = 30;
    public const DEFAULT_OT_HOURS_PER_DAY = 12;
    public const DEFAULT_OT_RATE_PER_HOUR = self::DEFAULT_OT_CALCULATION_AMOUNT / self::DEFAULT_OT_DAYS_PER_MONTH / self::DEFAULT_OT_HOURS_PER_DAY;

    protected $fillable = [
        'vehicle_monthly_log_id',
        'fixed_payment',
        'ot_minutes',
        'ot_hours',
        'ot_rate_per_hour',
        'ot_amount',
        'total_payment',
    ];

    public function monthlyLog()
    {
        return $this->belongsTo(VehicleMonthlyLog::class, 'vehicle_monthly_log_id');
    }

    public function getFormattedOtAttribute()
    {
        return VehicleMonthlyLog::formatMinutes($this->ot_minutes);
    }
}
