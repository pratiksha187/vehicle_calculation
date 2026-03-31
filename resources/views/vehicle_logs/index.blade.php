@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Monthly Vehicle Logs</h3>
    <a href="{{ route('vehicle-logs.create') }}" class="btn btn-primary">Create Monthly Log</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Vehicle</th>
                    <th>Period</th>
                    <th>Total KM</th>
                    <th>Diesel</th>
                    <th>OT Hours</th>
                    <th>Total Billing</th>
                    <th>Net Payable</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $key => $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $key }}</td>
                        <td>{{ $log->vehicle->display_name }}</td>
                        <td>{{ $log->from_date->format('d-m-Y') }} to {{ $log->to_date->format('d-m-Y') }}</td>
                        <td>{{ $log->total_km }}</td>
                        <td>{{ $log->diesel_total }}</td>
                        <td>{{ $log->formatted_ot }}</td>
                        <td>{{ number_format($log->total_billing_amount, 2) }}</td>
                        <td>{{ number_format($log->net_payable, 2) }}</td>
                        <td>
                            <a href="{{ route('vehicle-logs.show', $log->id) }}" class="btn btn-sm btn-info">View</a>
                             <a href="{{ route('vehicle-logs.invoice', $log->id) }}" class="btn btn-sm btn-success">Invoice</a>
                            <a href="{{ route('vehicle-logs.daily-entry', $log->id) }}" class="btn btn-sm btn-primary">Daily Entry</a>
                            <a href="{{ route('vehicle-logs.edit', $log->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('vehicle-logs.destroy', $log->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Delete this monthly log?')" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center">No monthly logs found.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $logs->links() }}
    </div>
</div>
@endsection