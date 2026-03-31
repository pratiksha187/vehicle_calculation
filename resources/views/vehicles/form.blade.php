<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Vehicle Name</label>
        <input type="text" name="vehicle_name" class="form-control" value="{{ old('vehicle_name', $vehicle->vehicle_name ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Vehicle Number</label>
        <input type="text" name="vehicle_number" class="form-control" value="{{ old('vehicle_number', $vehicle->vehicle_number ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Owner Name</label>
        <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name', $vehicle->owner_name ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Fixed Monthly Amount</label>
        <input type="number" step="0.01" name="fixed_monthly_amount" class="form-control" value="{{ old('fixed_monthly_amount', $vehicle->fixed_monthly_amount ?? 50000) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">OT Rate Per Hour</label>
        <input type="number" step="0.01" name="ot_rate_per_hour" class="form-control" value="{{ old('ot_rate_per_hour', $vehicle->ot_rate_per_hour ?? 55.56) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">TDS %</label>
        <input type="number" step="0.01" name="tds_percent" class="form-control" value="{{ old('tds_percent', $vehicle->tds_percent ?? 1) }}">
    </div>

    <div class="col-12">
        <button class="btn btn-success">Save</button>
        <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>