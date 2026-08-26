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
            Add driver name and Present / Absent / Half Day here for driver payment only. Vehicle daily entry and company monthly billing are separate.
        </div>

        <form action="{{ route('vehicle-logs.save-driver-details', $vehicle_log->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Sr No</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Driver Name</th>
                            <th>Attendance</th>
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
                                    <input type="text" name="entries[{{ $index }}][driver_name]" value="{{ $entry->driver_name ?: 'Rohit' }}" class="form-control form-control-sm" list="driverNames">
                                </td>
                                <td>
                                    <select name="entries[{{ $index }}][attendance_status]" class="form-select form-select-sm">
                                        <option value="present" @selected(($entry->attendance_status ?: 'present') === 'present')>Present</option>
                                        <option value="absent" @selected($entry->attendance_status === 'absent')>Absent</option>
                                        <option value="half_day" @selected($entry->attendance_status === 'half_day')>Half Day</option>
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
                <option value="Rohit">
                <option value="Driver 2">
            </datalist>

            @if($vehicle_log->driverPayments->isNotEmpty())
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-sm">
                        <thead class="table-secondary">
                            <tr>
                                <th>Driver</th>
                                <th>Monthly Payment</th>
                                <th>Extra Hrs Rate</th>
                                <th>Payable Day Payment</th>
                                <th>Total Payment</th>
                                <th>Advance Total</th>
                                <th>Net Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicle_log->driverPayments as $payment)
                                <tr>
                                    <td>{{ $payment->driver_name }}</td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="driver_payments[{{ $payment->id }}][monthly_payment]" value="{{ $payment->monthly_payment }}" class="form-control form-control-sm">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="driver_payments[{{ $payment->id }}][ot_rate_per_hour]" value="{{ $payment->ot_rate_per_hour }}" class="form-control form-control-sm">
                                    </td>
                                    <td>{{ number_format($payment->fixed_payment, 2) }}</td>
                                    <td>{{ number_format($payment->total_payment, 2) }}</td>
                                    <td>{{ number_format($payment->advance_payment, 2) }}</td>
                                    <td><strong>{{ number_format($payment->net_payment, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong>Advance Details</strong>
                                            <button type="button" class="btn btn-sm btn-outline-primary add-advance-row" data-payment-id="{{ $payment->id }}">Add Advance</button>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                        <th>SS / Proof</th>
                                                        <th>Remove</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="advanceRows{{ $payment->id }}">
                                                    @forelse($payment->advances as $advanceIndex => $advance)
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" name="driver_payments[{{ $payment->id }}][advances][{{ $advanceIndex }}][id]" value="{{ $advance->id }}">
                                                                <input type="date" name="driver_payments[{{ $payment->id }}][advances][{{ $advanceIndex }}][advance_date]" value="{{ optional($advance->advance_date)->format('Y-m-d') }}" class="form-control form-control-sm">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" min="0" name="driver_payments[{{ $payment->id }}][advances][{{ $advanceIndex }}][amount]" value="{{ $advance->amount }}" class="form-control form-control-sm">
                                                            </td>
                                                            <td>
                                                                <input type="file" name="driver_payments[{{ $payment->id }}][advances][{{ $advanceIndex }}][screenshot]" accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf" class="form-control form-control-sm">
                                                                @if($advance->screenshot)
                                                                    <a href="{{ asset('storage/' . $advance->screenshot) }}" target="_blank" class="small">View SS</a>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" name="driver_payments[{{ $payment->id }}][advances][{{ $advanceIndex }}][remove]" value="1">
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td>
                                                                <input type="date" name="driver_payments[{{ $payment->id }}][advances][0][advance_date]" class="form-control form-control-sm">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" min="0" name="driver_payments[{{ $payment->id }}][advances][0][amount]" class="form-control form-control-sm">
                                                            </td>
                                                            <td>
                                                                <input type="file" name="driver_payments[{{ $payment->id }}][advances][0][screenshot]" accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf" class="form-control form-control-sm">
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <button class="btn btn-success mt-3">Save Driver Details</button>
            <a href="{{ route('vehicle-logs.daily-entry', $vehicle_log->id) }}" class="btn btn-primary mt-3">Daily Entry</a>
            <a href="{{ route('vehicle-logs.driver-payment-slip', $vehicle_log->id) }}" class="btn btn-dark mt-3">Driver Payment</a>
        </form>

        @if($vehicle_log->driverPayments->isNotEmpty())
            @php
                $attendanceSummary = $vehicle_log->dailyEntries
                    ->groupBy(fn ($entry) => $entry->driver_name ?: 'Rohit')
                    ->map(fn ($entries) => [
                        'present' => $entries->filter(fn ($entry) => ($entry->attendance_status ?: 'present') === 'present')->count(),
                        'half_day' => $entries->filter(fn ($entry) => $entry->attendance_status === 'half_day')->count(),
                        'absent' => $entries->filter(fn ($entry) => $entry->attendance_status === 'absent')->count(),
                        'payable' => $entries->sum(fn ($entry) => match ($entry->attendance_status ?: 'present') {
                            'present' => 1,
                            'half_day' => 0.5,
                            default => 0,
                        }),
                    ]);
                $salaryWorkingDays = $vehicle_log->salaryWorkingDays();
            @endphp

            <div class="alert alert-secondary py-2 mt-4">
                Salary days: {{ $salaryWorkingDays }}. Sundays are included; mark Sunday Absent to cut that day's payment, or Half Day for 50% day payment.
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-sm">
                    <thead class="table-secondary">
                        <tr>
                            <th>Driver</th>
                            <th>Present Days</th>
                            <th>Half Days</th>
                            <th>Absent Days</th>
                            <th>Payable Days</th>
                            <th>Monthly Payment</th>
                            <th>Payable Day Payment</th>
                            <th>OT Hrs</th>
                            <th>Driver OT Rate</th>
                            <th>OT Amount</th>
                            <th>Calculation</th>
                            <th>Total Payment</th>
                            <th>Advance Details</th>
                            <th>Net Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicle_log->driverPayments as $payment)
                            <tr>
                                <td>{{ $payment->driver_name }}</td>
                                <td>{{ $attendanceSummary[$payment->driver_name]['present'] ?? 0 }}</td>
                                <td>{{ $attendanceSummary[$payment->driver_name]['half_day'] ?? 0 }}</td>
                                <td>{{ $attendanceSummary[$payment->driver_name]['absent'] ?? 0 }}</td>
                                <td>{{ number_format($attendanceSummary[$payment->driver_name]['payable'] ?? 0, 1) }}</td>
                                <td>{{ number_format($payment->monthly_payment, 2) }}</td>
                                <td>{{ number_format($payment->fixed_payment, 2) }}</td>
                                <td>{{ $payment->formatted_ot }}</td>
                                <td>{{ number_format($payment->ot_rate_per_hour, 2) }}</td>
                                <td>{{ number_format($payment->ot_amount, 2) }}</td>
                                <td>{{ number_format($payment->monthly_payment, 2) }} / {{ $salaryWorkingDays }} days x {{ number_format($attendanceSummary[$payment->driver_name]['payable'] ?? 0, 1) }} payable = {{ number_format($payment->fixed_payment, 2) }} + ({{ $payment->formatted_ot }} = {{ number_format($payment->ot_hours, 2) }} hrs x {{ number_format($payment->ot_rate_per_hour, 2) }})</td>
                                <td><strong>{{ number_format($payment->total_payment, 2) }}</strong></td>
                                <td>
                                    <strong>{{ number_format($payment->advance_payment, 2) }}</strong>
                                    @if($payment->advances->isNotEmpty())
                                        <div class="small">
                                            @foreach($payment->advances as $advance)
                                                <div>
                                                    {{ $advance->advance_date ? $advance->advance_date->format('d/m/Y') : '-' }}:
                                                    {{ number_format($advance->amount, 2) }}
                                                    @if($advance->screenshot)
                                                        - <a href="{{ asset('storage/' . $advance->screenshot) }}" target="_blank">SS</a>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="small">No advance details</div>
                                    @endif
                                </td>
                                <td><strong>{{ number_format($payment->net_payment, 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
    document.querySelectorAll('.add-advance-row').forEach((button) => {
        button.addEventListener('click', () => {
            const paymentId = button.dataset.paymentId;
            const tbody = document.getElementById(`advanceRows${paymentId}`);
            const index = tbody.querySelectorAll('tr').length;
            const row = document.createElement('tr');

            row.innerHTML = `
                <td>
                    <input type="date" name="driver_payments[${paymentId}][advances][${index}][advance_date]" class="form-control form-control-sm">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="driver_payments[${paymentId}][advances][${index}][amount]" class="form-control form-control-sm">
                </td>
                <td>
                    <input type="file" name="driver_payments[${paymentId}][advances][${index}][screenshot]" accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf" class="form-control form-control-sm">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-new-advance-row">Remove</button>
                </td>
            `;

            tbody.appendChild(row);
            row.querySelector('.remove-new-advance-row').addEventListener('click', () => row.remove());
        });
    });
</script>
@endsection
