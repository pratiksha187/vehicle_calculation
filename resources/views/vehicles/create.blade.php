@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><h4>Add Vehicle</h4></div>
    <div class="card-body">
        <form action="{{ route('vehicles.store') }}" method="POST">
            @csrf
            @include('vehicles.form')
        </form>
    </div>
</div>
@endsection