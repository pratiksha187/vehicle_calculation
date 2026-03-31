@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><h4>Create Monthly Sheet</h4></div>
    <div class="card-body">
        <form action="{{ route('vehicle-logs.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Vehicle</label>
                    <select name="vehicle_id" class="form-select" required>
                        <option value="">Select Vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" required>
                </div>

                <div class="col-12">
                    <button class="btn btn-success">Create Monthly Sheet</button>
                    <a href="{{ route('vehicle-logs.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection