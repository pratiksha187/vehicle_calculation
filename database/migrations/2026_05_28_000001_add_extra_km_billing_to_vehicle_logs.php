<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $defaultKmLimit = 3500;
        $defaultExtraKmRate = 5;

        Schema::table('vehicles', function (Blueprint $table) {
            $table->integer('km_limit')->default(3500)->after('fixed_monthly_amount');
            $table->decimal('extra_km_rate', 12, 2)->default(5)->after('km_limit');
        });

        Schema::table('vehicle_monthly_logs', function (Blueprint $table) {
            $table->integer('km_limit')->default(3500)->after('fixed_monthly_amount');
            $table->decimal('extra_km_rate', 12, 2)->default(5)->after('km_limit');
            $table->integer('extra_km')->default(0)->after('extra_km_rate');
            $table->decimal('extra_km_amount', 12, 2)->default(0)->after('extra_km');
        });

        DB::table('vehicle_monthly_logs')->orderBy('id')->chunkById(100, function ($logs) use ($defaultKmLimit, $defaultExtraKmRate) {
            foreach ($logs as $log) {
                $kmLimit = $defaultKmLimit;
                $extraKmRate = $defaultExtraKmRate;
                $extraKm = max(0, (int)$log->total_km - $kmLimit);
                $extraKmAmount = $extraKm * $extraKmRate;
                $totalBilling = (float)$log->fixed_monthly_amount + (float)$log->total_ot_amount + $extraKmAmount;
                $tdsAmount = ($totalBilling * (float)$log->tds_percent) / 100;

                DB::table('vehicle_monthly_logs')
                    ->where('id', $log->id)
                    ->update([
                        'km_limit' => $kmLimit,
                        'extra_km_rate' => $extraKmRate,
                        'extra_km' => $extraKm,
                        'extra_km_amount' => round($extraKmAmount, 2),
                        'total_billing_amount' => round($totalBilling, 2),
                        'tds_amount' => round($tdsAmount, 2),
                        'net_payable' => round($totalBilling - $tdsAmount, 2),
                    ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_monthly_logs', function (Blueprint $table) {
            $table->dropColumn([
                'km_limit',
                'extra_km_rate',
                'extra_km',
                'extra_km_amount',
            ]);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'km_limit',
                'extra_km_rate',
            ]);
        });
    }
};
