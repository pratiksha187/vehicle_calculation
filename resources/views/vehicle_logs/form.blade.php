@php
    $entries = old('entries', isset($log) && $log ? $log->dailyEntries->map(function($e){
        return [
            'entry_date' => $e->entry_date->format('Y-m-d'),
            'challan_no' => $e->challan_no,
            'diesel_added' => $e->diesel_added,
            'start_reading' => $e->start_reading,
            'end_reading' => $e->end_reading,
            'in_time' => $e->in_time ? date('H:i', strtotime($e->in_time)) : '',
            'out_time' => $e->out_time ? date('H:i', strtotime($e->out_time)) : '',
            'remark' => $e->remark,
        ];
    })->toArray() : [[]]);
@endphp

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label class="form-label">Vehicle</label>
        <select name="vehicle_id" class="form-select" required>
            <option value="">Select Vehicle</option>
            @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}"
                    {{ old('vehicle_id', $log->vehicle_id ?? '') == $vehicle->id ? 'selected' : '' }}>
                    {{ $vehicle->display_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">From Date</label>
        <input type="date" name="from_date" class="form-control" value="{{ old('from_date', isset($log) && $log ? $log->from_date->format('Y-m-d') : '') }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">To Date</label>
        <input type="date" name="to_date" class="form-control" value="{{ old('to_date', isset($log) && $log ? $log->to_date->format('Y-m-d') : '') }}" required>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">Daily Entries</h5>
    <button type="button" class="btn btn-sm btn-primary" id="addRow">Add Row</button>
</div>

<div class="table-responsive">
    <table class="table table-bordered" id="entryTable">
        <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Challan No</th>
                <th>Diesel</th>
                <th>Start Reading</th>
                <th>End Reading</th>
                <th>In Time</th>
                <th>Out Time</th>
                <th>Remark</th>
                <th class="no-print">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $index => $entry)
                <tr>
                    <td><input type="date" name="entries[{{ $index }}][entry_date]" class="form-control" value="{{ $entry['entry_date'] ?? '' }}" required></td>
                    <td><input type="text" name="entries[{{ $index }}][challan_no]" class="form-control" value="{{ $entry['challan_no'] ?? '' }}"></td>
                    <td><input type="number" step="0.01" name="entries[{{ $index }}][diesel_added]" class="form-control" value="{{ $entry['diesel_added'] ?? 0 }}"></td>
                    <td><input type="number" name="entries[{{ $index }}][start_reading]" class="form-control" value="{{ $entry['start_reading'] ?? '' }}" required></td>
                    <td><input type="number" name="entries[{{ $index }}][end_reading]" class="form-control" value="{{ $entry['end_reading'] ?? '' }}" required></td>
                    <td><input type="time" name="entries[{{ $index }}][in_time]" class="form-control" value="{{ $entry['in_time'] ?? '' }}"></td>
                    <td><input type="time" name="entries[{{ $index }}][out_time]" class="form-control" value="{{ $entry['out_time'] ?? '' }}"></td>
                    <td><input type="text" name="entries[{{ $index }}][remark]" class="form-control" value="{{ $entry['remark'] ?? '' }}"></td>
                    <td class="no-print"><button type="button" class="btn btn-sm btn-danger removeRow">X</button></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-3">
    <button class="btn btn-success">Save Monthly Log</button>
    <a href="{{ route('vehicle-logs.index') }}" class="btn btn-secondary">Back</a>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = {{ count($entries) }};

    document.getElementById('addRow').addEventListener('click', function () {
        const tbody = document.querySelector('#entryTable tbody');
        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td><input type="date" name="entries[${rowIndex}][entry_date]" class="form-control" required></td>
            <td><input type="text" name="entries[${rowIndex}][challan_no]" class="form-control"></td>
            <td><input type="number" step="0.01" name="entries[${rowIndex}][diesel_added]" class="form-control" value="0"></td>
            <td><input type="number" name="entries[${rowIndex}][start_reading]" class="form-control" required></td>
            <td><input type="number" name="entries[${rowIndex}][end_reading]" class="form-control" required></td>
            <td><input type="time" name="entries[${rowIndex}][in_time]" class="form-control"></td>
            <td><input type="time" name="entries[${rowIndex}][out_time]" class="form-control"></td>
            <td><input type="text" name="entries[${rowIndex}][remark]" class="form-control"></td>
            <td class="no-print"><button type="button" class="btn btn-sm btn-danger removeRow">X</button></td>
        `;

        tbody.appendChild(tr);
        rowIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRow')) {
            const rows = document.querySelectorAll('#entryTable tbody tr');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
            } else {
                alert('At least one row is required.');
            }
        }
    });
});
</script>
@endpush