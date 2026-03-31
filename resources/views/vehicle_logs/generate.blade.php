@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">
            {{ $fromDate->format('d-m-Y') }} to {{ $toDate->format('d-m-Y') }}
            {{ $vehicle->vehicle_name }} - {{ $vehicle->vehicle_number }}
        </h4>
    </div>
    <div class="card-body">
        <form action="{{ route('vehicle-logs.store') }}" method="POST">
            @csrf

            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
            <input type="hidden" name="from_date" value="{{ $fromDate->format('Y-m-d') }}">
            <input type="hidden" name="to_date" value="{{ $toDate->format('Y-m-d') }}">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-warning">
                        <tr>
                            <th>Sr. No.</th>
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
                        @foreach($entries as $index => $entry)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($entry['entry_date'])->format('d/m/Y') }}
                                    <input type="hidden" name="entries[{{ $index }}][entry_date]" value="{{ $entry['entry_date'] }}">
                                </td>

                                <td>
                                    {{ $entry['day'] }}
                                </td>

                                <td>
                                    <input type="text" name="entries[{{ $index }}][challan_no]" class="form-control form-control-sm">
                                </td>

                                <td>
                                    <input type="number" step="0.01" name="entries[{{ $index }}][diesel_added]" class="form-control form-control-sm" value="0">
                                </td>

                                <td>
                                    <input type="number" name="entries[{{ $index }}][start_reading]" class="form-control form-control-sm">
                                </td>

                                <td>
                                    <input type="number" name="entries[{{ $index }}][end_reading]" class="form-control form-control-sm">
                                </td>

                                <td>
                                    <input type="time" name="entries[{{ $index }}][in_time]" class="form-control form-control-sm">
                                </td>

                                <td>
                                    <input type="time" name="entries[{{ $index }}][out_time]" class="form-control form-control-sm">
                                </td>

                                <td>
                                    <input type="text" name="entries[{{ $index }}][remark]" class="form-control form-control-sm">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <button class="btn btn-success">Save Monthly Log</button>
                <a href="{{ route('vehicle-logs.create') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection