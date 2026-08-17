<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleDriverPaymentAdvance extends Model
{
    protected $fillable = [
        'vehicle_driver_payment_id',
        'advance_date',
        'amount',
        'screenshot',
    ];

    protected $casts = [
        'advance_date' => 'date',
    ];

    public function driverPayment()
    {
        return $this->belongsTo(VehicleDriverPayment::class, 'vehicle_driver_payment_id');
    }
}
