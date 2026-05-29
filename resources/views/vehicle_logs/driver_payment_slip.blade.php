@extends('layouts.app')

@section('content')
@php
    $driverPayment = $vehicle_log->driverPayment;
@endphp

<style>
    .driver-slip {
        max-width: 760px;
        margin: auto;
        background: #fff;
        border: 1px solid #222;
        padding: 24px;
    }

    .driver-slip-title {
        text-align: center;
        font-size: 22px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 18px;
    }

    .driver-slip-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
    }

    .driver-slip-table th,
    .driver-slip-table td {
        border: 1px solid #222;
        padding: 9px;
        font-size: 14px;
    }

    .driver-slip-table th {
        width: 45%;
        background: #f3f3f3;
    }

    .right {
        text-align: right;
    }

    .signature-row {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        margin-top: 54px;
    }

    .signature-box {
        width: 45%;
        border-top: 1px solid #222;
        padding-top: 8px;
        text-align: center;
        font-size: 13px;
    }

    @media print {
        .container {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
        }

        .card,
        .card-body {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .driver-slip {
            border: none;
            max-width: 100%;
            padding: 0;
        }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <h3>Driver Payment Slip</h3>
    <div>
        <a href="{{ route('vehicle-logs.index') }}" class="btn btn-secondary">Back</a>
        <button onclick="window.print()" class="btn btn-dark">Print</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="driver-slip">
            <div class="driver-slip-title">Driver Payment Slip</div>

            <table class="driver-slip-table">
                <tr>
                    <th>Vehicle</th>
                    <td>{{ $vehicle_log->vehicle->vehicle_name }} - {{ $vehicle_log->vehicle->vehicle_number }}</td>
                </tr>
                <tr>
                    <th>Owner Name</th>
                    <td>{{ $vehicle_log->vehicle->owner_name }}</td>
                </tr>
                <tr>
                    <th>Payment Period</th>
                    <td>{{ $vehicle_log->from_date->format('d-m-Y') }} to {{ $vehicle_log->to_date->format('d-m-Y') }}</td>
                </tr>
            </table>

            <table class="driver-slip-table">
                <tr>
                    <th>Fixed Driver Payment</th>
                    <td class="right">Rs. {{ number_format($driverPayment->fixed_payment ?? 20000, 2) }}</td>
                </tr>
                <tr>
                    <th>Total Working OT After 12 Hours</th>
                    <td class="right">{{ $driverPayment->formatted_ot ?? '00:00' }}</td>
                </tr>
                <tr>
                    <th>Driver OT Rate</th>
                    <td class="right">Rs. {{ number_format($driverPayment->ot_rate_per_hour ?? 50, 2) }} / hour</td>
                </tr>
                <tr>
                    <th>Driver OT Amount</th>
                    <td class="right">Rs. {{ number_format($driverPayment->ot_amount ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <th>Total Driver Payment</th>
                    <td class="right"><strong>Rs. {{ number_format($driverPayment->total_payment ?? 20000, 2) }}</strong></td>
                </tr>
            </table>

            <div class="signature-row">
                <div class="signature-box">Driver Signature</div>
                <div class="signature-box">Authorised Signature</div>
            </div>
        </div>
    </div>
</div>
@endsection
