@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Edit Monthly Log - {{ $vehicle_log->vehicle->vehicle_name }} - {{ $vehicle_log->vehicle->vehicle_number }}</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('vehicle-logs.update', $vehicle_log->id) }}" method="POST">
            @csrf
            @method('PUT')

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
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicle_log->dailyEntries as $index => $entry)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $entry->entry_date->format('d/m/Y') }}
                                    <input type="hidden" name="entries[{{ $index }}][entry_date]" value="{{ $entry->entry_date->format('Y-m-d') }}">
                                </td>
                                <td>{{ $entry->day }}</td>
                                <td><input type="text" name="entries[{{ $index }}][challan_no]" value="{{ $entry->challan_no }}" class="form-control form-control-sm"></td>
                                <td><input type="number" step="0.01" name="entries[{{ $index }}][diesel_added]" value="{{ $entry->diesel_added }}" class="form-control form-control-sm"></td>
                                <td><input type="number" name="entries[{{ $index }}][start_reading]" value="{{ $entry->start_reading }}" class="form-control form-control-sm"></td>
                                <td><input type="number" name="entries[{{ $index }}][end_reading]" value="{{ $entry->end_reading }}" class="form-control form-control-sm"></td>
                                <td><input type="time" name="entries[{{ $index }}][in_time]" value="{{ $entry->in_time ? date('H:i', strtotime($entry->in_time)) : '' }}" class="form-control form-control-sm"></td>
                                <td><input type="time" name="entries[{{ $index }}][out_time]" value="{{ $entry->out_time ? date('H:i', strtotime($entry->out_time)) : '' }}" class="form-control form-control-sm"></td>
                                <td><input type="text" name="entries[{{ $index }}][remark]" value="{{ $entry->remark }}" class="form-control form-control-sm"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button class="btn btn-success mt-3">Update Monthly Log</button>
            <a href="{{ route('vehicle-logs.index') }}" class="btn btn-secondary mt-3">Back</a>
        </form>
    </div>
</div>
@endsection