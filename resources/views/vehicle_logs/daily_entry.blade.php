@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            Daily Entry - {{ $vehicle_log->vehicle->vehicle_name }} - {{ $vehicle_log->vehicle->vehicle_number }}
        </h4>
        <a href="{{ route('vehicle-logs.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card-body">
        <div class="mb-3">
            <strong>Period:</strong>
            {{ $vehicle_log->from_date->format('d-m-Y') }} to {{ $vehicle_log->to_date->format('d-m-Y') }}
        </div>

        <form action="{{ route('vehicle-logs.save-daily-entry', $vehicle_log->id) }}" method="POST">
            @csrf

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-dark">
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
                                <td>
                                    {{ $entry->entry_date->format('d/m/Y') }}
                                    <input type="hidden" name="entries[{{ $index }}][id]" value="{{ $entry->id }}">
                                </td>
                                <td>{{ $entry->day }}</td>
                                <td><input type="text" name="entries[{{ $index }}][challan_no]" value="{{ $entry->challan_no }}" class="form-control form-control-sm"></td>
                                <td><input type="number" step="0.01" name="entries[{{ $index }}][diesel_added]" value="{{ $entry->diesel_added }}" class="form-control form-control-sm diesel"></td>
                                <td><input type="number" name="entries[{{ $index }}][start_reading]" value="{{ $entry->start_reading }}" class="form-control form-control-sm start-reading"></td>
                                <td><input type="number" name="entries[{{ $index }}][end_reading]" value="{{ $entry->end_reading }}" class="form-control form-control-sm end-reading"></td>
                                <td><input type="text" value="{{ $entry->total_km }}" class="form-control form-control-sm total-km" readonly></td>
                                <td><input type="time" name="entries[{{ $index }}][in_time]" value="{{ $entry->in_time ? date('H:i', strtotime($entry->in_time)) : '' }}" class="form-control form-control-sm in-time"></td>
                                <td><input type="time" name="entries[{{ $index }}][out_time]" value="{{ $entry->out_time ? date('H:i', strtotime($entry->out_time)) : '' }}" class="form-control form-control-sm out-time"></td>
                                <td><input type="text" value="{{ $entry->formatted_total_hours }}" class="form-control form-control-sm total-hours" readonly></td>
                                <td><input type="text" value="{{ $entry->formatted_ot_hours }}" class="form-control form-control-sm ot-hours" readonly></td>
                                <td><input type="text" name="entries[{{ $index }}][remark]" value="{{ $entry->remark }}" class="form-control form-control-sm"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button class="btn btn-success mt-3">Save Daily Entries</button>
            <a href="{{ route('vehicle-logs.show', $vehicle_log->id) }}" class="btn btn-info mt-3">View Bill</a>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function formatMinutes(minutes) {
    let hrs = Math.floor(minutes / 60);
    let mins = minutes % 60;
    return String(hrs).padStart(2, '0') + ':' + String(mins).padStart(2, '0');
}

function calculateRow(row) {
    let startReading = parseFloat(row.querySelector('.start-reading').value) || 0;
    let endReading = parseFloat(row.querySelector('.end-reading').value) || 0;
    let totalKmField = row.querySelector('.total-km');

    let inTime = row.querySelector('.in-time').value;
    let outTime = row.querySelector('.out-time').value;
    let totalHoursField = row.querySelector('.total-hours');
    let otHoursField = row.querySelector('.ot-hours');

    let totalKm = endReading >= startReading ? (endReading - startReading) : 0;
    totalKmField.value = totalKm;

    if (inTime && outTime) {
        let inDate = new Date(`2000-01-01T${inTime}`);
        let outDate = new Date(`2000-01-01T${outTime}`);

        if (outDate < inDate) {
            outDate.setDate(outDate.getDate() + 1);
        }

        let diffMinutes = Math.floor((outDate - inDate) / 60000);
        let otMinutes = Math.max(0, diffMinutes - 720);

        totalHoursField.value = formatMinutes(diffMinutes);
        otHoursField.value = formatMinutes(otMinutes);
    } else {
        totalHoursField.value = '00:00';
        otHoursField.value = '00:00';
    }
}

document.addEventListener('input', function(e) {
    let row = e.target.closest('tr');
    if (row) calculateRow(row);
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('tbody tr').forEach(function(row) {
        calculateRow(row);
    });
});
</script>
@endpush