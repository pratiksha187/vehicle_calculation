@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <h3>Vehicle Bill</h3>
    <div>
        <a href="{{ route('vehicle-logs.daily-entry', $vehicle_log->id) }}" class="btn btn-primary">Daily Entry</a>
        <button onclick="window.print()" class="btn btn-dark">Print</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="text-center fw-bold mb-4">
            {{ $vehicle_log->from_date->format('d-m-Y') }} to {{ $vehicle_log->to_date->format('d-m-Y') }}
            {{ $vehicle_log->vehicle->vehicle_name }} - {{ $vehicle_log->vehicle->vehicle_number }}
        </h4>

        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-warning">
                    <tr>
                        <th>Sr No</th>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Challan No</th>
                        <th>Diesel Added</th>
                        <th>Start Reading</th>
                        <th>End Reading</th>
                        <th>Total KM</th>
                        <th>In Time</th>
                        <th>Out Time</th>
                        <th>Total Hrs</th>
                        <th>OT Hrs</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicle_log->dailyEntries as $index => $entry)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $entry->entry_date->format('d/m/Y') }}</td>
                            <td>{{ $entry->day }}</td>
                            <td>{{ $entry->challan_no }}</td>
                            <td>{{ $entry->diesel_added }}</td>
                            <td>{{ $entry->start_reading }}</td>
                            <td>{{ $entry->end_reading }}</td>
                            <td>{{ $entry->total_km }}</td>
                            <td>{{ $entry->in_time ? date('h:i A', strtotime($entry->in_time)) : '' }}</td>
                            <td>{{ $entry->out_time ? date('h:i A', strtotime($entry->out_time)) : '' }}</td>
                            <td>{{ $entry->formatted_total_hours }}</td>
                            <td>{{ $entry->formatted_ot_hours }}</td>
                            <td>{{ $entry->remark }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row mt-4">
            <div class="col-md-5">
                <table class="table table-bordered">
                    <tr><th>Opening Reading</th><td>{{ $vehicle_log->opening_reading }}</td></tr>
                    <tr><th>Closing Reading</th><td>{{ $vehicle_log->closing_reading }}</td></tr>
                    <tr><th>Total KM</th><td>{{ $vehicle_log->total_km }}</td></tr>
                    <tr><th>Diesel Total</th><td>{{ $vehicle_log->diesel_total }}</td></tr>
                    <tr><th>Average</th><td>{{ $vehicle_log->average_kmpl }}</td></tr>
                </table>
            </div>

            <div class="col-md-5">
                <table class="table table-bordered">
                    <tr><th>Fixed Monthly Amount</th><td>{{ number_format($vehicle_log->fixed_monthly_amount, 2) }}</td></tr>
                    <tr><th>OT Hrs</th><td>{{ $vehicle_log->formatted_ot }}</td></tr>
                    <tr><th>OT Rate</th><td>{{ number_format($vehicle_log->vehicle->ot_rate_per_hour, 2) }}</td></tr>
                    <tr><th>Total OT Amount</th><td>{{ number_format($vehicle_log->total_ot_amount, 2) }}</td></tr>
                    <tr><th>Total Billing Amount</th><td>{{ number_format($vehicle_log->total_billing_amount, 2) }}</td></tr>
                    <tr><th>TDS {{ $vehicle_log->tds_percent }}%</th><td>{{ number_format($vehicle_log->tds_amount, 2) }}</td></tr>
                    <tr><th>Net Payable</th><td><strong>{{ number_format($vehicle_log->net_payable, 2) }}</strong></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection