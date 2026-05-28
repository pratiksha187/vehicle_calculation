<?php

use App\Models\VehicleMonthlyLog;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('vehicle-logs:recalculate-extra-km {id?}', function () {
    $id = $this->argument('id');

    $logs = VehicleMonthlyLog::query()
        ->when($id, fn ($query) => $query->whereKey($id))
        ->get();

    foreach ($logs as $log) {
        $log->syncBillingTotalsFromSavedTotals();
    }

    $this->info("Recalculated {$logs->count()} monthly log(s).");
})->purpose('Recalculate extra kilometer billing for monthly vehicle logs');
