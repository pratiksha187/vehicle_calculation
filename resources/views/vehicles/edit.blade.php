@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><h4>Edit Vehicle</h4></div>
    <div class="card-body">
        <form action="{{ route('vehicles.update', $vehicle->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('vehicles.form')
        </form>
    </div>
</div>
@endsection