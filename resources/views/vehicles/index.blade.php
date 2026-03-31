@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Vehicles</h3>
    <a href="{{ route('vehicles.create') }}" class="btn btn-primary">Add Vehicle</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Vehicle Name</th>
                    <th>Vehicle Number</th>
                    <th>Owner</th>
                    <th>Fixed Amount</th>
                    <th>OT Rate</th>
                    <th>TDS %</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $key => $vehicle)
                    <tr>
                        <td>{{ $vehicles->firstItem() + $key }}</td>
                        <td>{{ $vehicle->vehicle_name }}</td>
                        <td>{{ $vehicle->vehicle_number }}</td>
                        <td>{{ $vehicle->owner_name }}</td>
                        <td>{{ number_format($vehicle->fixed_monthly_amount, 2) }}</td>
                        <td>{{ number_format($vehicle->ot_rate_per_hour, 2) }}</td>
                        <td>{{ $vehicle->tds_percent }}</td>
                        <td>
                            <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Delete vehicle?')" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No vehicles found.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $vehicles->links() }}
    </div>
</div>
@endsection