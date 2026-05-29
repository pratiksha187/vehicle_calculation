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
            $table->foreignId('vehicle_monthly_log_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('fixed_payment', 12, 2)->default(20000);
            $table->integer('ot_minutes')->default(0);
            $table->decimal('ot_hours', 12, 2)->default(0);
            $table->decimal('ot_rate_per_hour', 12, 2)->default(50);
            $table->decimal('ot_amount', 12, 2)->default(0);
            $table->decimal('total_payment', 12, 2)->default(20000);
            $table->timestamps();
        });

        DB::table('vehicle_monthly_logs')->orderBy('id')->chunkById(100, function ($logs) {
            foreach ($logs as $log) {
                $otHours = ((int)$log->total_ot_minutes) / 60;
                $otAmount = $otHours * 50;

                DB::table('vehicle_driver_payments')->insert([
                    'vehicle_monthly_log_id' => $log->id,
                    'fixed_payment' => 20000,
                    'ot_minutes' => (int)$log->total_ot_minutes,
                    'ot_hours' => round($otHours, 2),
                    'ot_rate_per_hour' => 50,
                    'ot_amount' => round($otAmount, 2),
                    'total_payment' => round(20000 + $otAmount, 2),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_driver_payments');
    }
};
