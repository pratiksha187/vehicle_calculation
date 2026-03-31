<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Payment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f1f3f7; }
        .card { border: 0; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .table th, .table td { vertical-align: middle; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .card { box-shadow: none; }
        }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark no-print">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('vehicle-logs.index') }}">Vehicle Payment System</a>
        <div class="navbar-nav">
            <a class="nav-link" href="{{ route('vehicles.index') }}">Vehicles</a>
            <a class="nav-link" href="{{ route('vehicle-logs.index') }}">Monthly Logs</a>
            <a class="nav-link" href="{{ route('vehicle-logs.create') }}">Create Log</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>