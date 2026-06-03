<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicle_driver_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_monthly_log_id')->constrained()->cascadeOnDelete();
            $table->string('driver_name')->default('Rohit');
            $table->decimal('fixed_payment', 12, 2)->default(20000);
            $table->integer('ot_minutes')->default(0);
            $table->decimal('ot_hours', 12, 2)->default(0);
            $table->decimal('ot_rate_per_hour', 12, 2)->default(50);
            $table->decimal('ot_amount', 12, 2)->default(0);
            $table->decimal('total_payment', 12, 2)->default(20000);
            $table->timestamps();

            $table->unique(['vehicle_monthly_log_id', 'driver_name'], 'driver_payment_unique');
        });

        DB::table('vehicle_monthly_logs')->orderBy('id')->chunkById(100, function ($logs) {
            foreach ($logs as $log) {
                $entries = DB::table('vehicle_daily_entries')
                    ->select('driver_name', DB::raw('SUM(ot_minutes) as ot_minutes'))
                    ->where('vehicle_monthly_log_id', $log->id)
                    ->groupBy('driver_name')
                    ->get();

                if ($entries->isEmpty()) {
                    $entries = collect([(object)[
                        'driver_name' => 'Rohit',
                        'ot_minutes' => (int)$log->total_ot_minutes,
                    ]]);
                }

                foreach ($entries as $entry) {
                    $otHours = ((int)$entry->ot_minutes) / 60;
                    $otAmount = $otHours * 50;

                    DB::table('vehicle_driver_payments')->insert([
                        'vehicle_monthly_log_id' => $log->id,
                        'driver_name' => $entry->driver_name ?: 'Rohit',
                        'fixed_payment' => 20000,
                        'ot_minutes' => (int)$entry->ot_minutes,
                        'ot_hours' => round($otHours, 2),
                        'ot_rate_per_hour' => 50,
                        'ot_amount' => round($otAmount, 2),
                        'total_payment' => round(20000 + $otAmount, 2),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_driver_payments');
    }
};
