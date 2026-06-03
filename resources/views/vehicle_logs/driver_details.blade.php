@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            Driver Details - {{ $vehicle_log->vehicle->vehicle_name }} - {{ $vehicle_log->vehicle->vehicle_number }}
        </h4>
        <a href="{{ route('vehicle-logs.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card-body">
        <div class="mb-3">
            <strong>Period:</strong>
            {{ $vehicle_log->from_date->format('d-m-Y') }} to {{ $vehicle_log->to_date->format('d-m-Y') }}
        </div>

        <div class="alert alert-info py-2">
            Add driver name and Present / Absent here. Driver monthly fixed payment is Rs. 20,000, calculated only for present days. Driver OT is Rs. 50/hour.
        </div>

        <form action="{{ route('vehicle-logs.save-driver-details', $vehicle_log->id) }}" method="POST">
            @csrf

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Sr No</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Driver Name</th>
                            <th>Present / Absent</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicle_log->dailyEntries as $index => $entry)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $entry->entry_date->format('d/m/Y') }}
                                    <input type="hidden" name="entries[{{ $index }}][id]" value="{{ $entry->id }}">
                                </td>
                                <td>{{ $entry->day }}</td>
                                <td>
                                    <input type="text" name="entries[{{ $index }}][driver_name]" value="{{ $entry->driver_name ?: 'Driver 1' }}" class="form-control form-control-sm" list="driverNames">
                                </td>
                                <td>
                                    <select name="entries[{{ $index }}][attendance_status]" class="form-select form-select-sm">
                                        <option value="present" @selected(($entry->attendance_status ?: 'present') === 'present')>Present</option>
                                        <option value="absent" @selected($entry->attendance_status === 'absent')>Absent</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="entries[{{ $index }}][remark]" value="{{ $entry->remark }}" class="form-control form-control-sm">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <datalist id="driverNames">
                <option value="Driver 1">
                <option value="Driver 2">
            </datalist>

            <button class="btn btn-success mt-3">Save Driver Details</button>
            <a href="{{ route('vehicle-logs.daily-entry', $vehicle_log->id) }}" class="btn btn-primary mt-3">Daily Entry</a>
            <a href="{{ route('vehicle-logs.driver-payment-slip', $vehicle_log->id) }}" class="btn btn-dark mt-3">Driver Payment</a>
        </form>

        @if($vehicle_log->driverPayments->isNotEmpty())
            @php
                $attendanceSummary = $vehicle_log->dailyEntries
                    ->groupBy(fn ($entry) => $entry->driver_name ?: 'Driver 1')
                    ->map(fn ($entries) => [
                        'present' => $entries->filter(fn ($entry) => ($entry->attendance_status ?: 'present') === 'present')->count(),
                        'absent' => $entries->where('attendance_status', 'absent')->count(),
                    ]);
            @endphp

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-sm">
                    <thead class="table-secondary">
                        <tr>
                            <th>Driver</th>
                            <th>Present Days</th>
                            <th>Absent Days</th>
                            <th>Present Day Payment</th>
                            <th>OT Hrs</th>
                            <th>Driver OT Rate</th>
                            <th>OT Amount</th>
                            <th>Calculation</th>
                            <th>Total Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicle_log->driverPayments as $payment)
                            <tr>
                                <td>{{ $payment->driver_name }}</td>
                                <td>{{ $attendanceSummary[$payment->driver_name]['present'] ?? 0 }}</td>
                                <td>{{ $attendanceSummary[$payment->driver_name]['absent'] ?? 0 }}</td>
                                <td>{{ number_format($payment->fixed_payment, 2) }}</td>
                                <td>{{ $payment->formatted_ot }}</td>
                                <td>{{ number_format($payment->ot_rate_per_hour, 2) }}</td>
                                <td>{{ number_format($payment->ot_amount, 2) }}</td>
                                <td>{{ number_format($payment->fixed_payment, 2) }} + ({{ number_format($payment->ot_hours, 2) }} x {{ number_format($payment->ot_rate_per_hour, 2) }})</td>
                                <td><strong>{{ number_format($payment->total_payment, 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
