<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleDailyEntry extends Model
{
    protected $fillable = [
        'vehicle_monthly_log_id',
        'entry_date',
        'day',
        'challan_no',
        'diesel_added',
        'start_reading',
        'end_reading',
        'total_km',
        'in_time',
        'out_time',
        'total_minutes',
        'ot_minutes',
        'remark',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function monthlyLog()
    {
        return $this->belongsTo(VehicleMonthlyLog::class, 'vehicle_monthly_log_id');
    }

    public function getFormattedTotalHoursAttribute()
    {
        return VehicleMonthlyLog::formatMinutes($this->total_minutes);
    }

    public function getFormattedOtHoursAttribute()
    {
        return VehicleMonthlyLog::formatMinutes($this->ot_minutes);
    }
}