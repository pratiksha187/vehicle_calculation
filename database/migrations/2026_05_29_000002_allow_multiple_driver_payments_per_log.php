<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('vehicle_driver_payments', 'driver_name')) {
            Schema::table('vehicle_driver_payments', function (Blueprint $table) {
                $table->string('driver_name')->default('Driver 1')->after('vehicle_monthly_log_id');
            });
        }

        if (!$this->indexExists('vehicle_driver_payments', 'vehicle_driver_payments_vehicle_monthly_log_id_index')) {
            Schema::table('vehicle_driver_payments', function (Blueprint $table) {
                $table->index('vehicle_monthly_log_id', 'vehicle_driver_payments_vehicle_monthly_log_id_index');
            });
        }

        if ($this->indexExists('vehicle_driver_payments', 'vehicle_driver_payments_vehicle_monthly_log_id_unique')) {
            Schema::table('vehicle_driver_payments', function (Blueprint $table) {
                $table->dropUnique('vehicle_driver_payments_vehicle_monthly_log_id_unique');
            });
        }

        if (!$this->indexExists('vehicle_driver_payments', 'driver_payment_unique')) {
            Schema::table('vehicle_driver_payments', function (Blueprint $table) {
                $table->unique(['vehicle_monthly_log_id', 'driver_name'], 'driver_payment_unique');
            });
        }

        DB::table('vehicle_driver_payments')
            ->whereNull('driver_name')
            ->orWhere('driver_name', '')
            ->update(['driver_name' => 'Driver 1']);
    }

    public function down(): void
    {
        if ($this->indexExists('vehicle_driver_payments', 'driver_payment_unique')) {
            Schema::table('vehicle_driver_payments', function (Blueprint $table) {
                $table->dropUnique('driver_payment_unique');
            });
        }

        if (Schema::hasColumn('vehicle_driver_payments', 'driver_name')) {
            Schema::table('vehicle_driver_payments', function (Blueprint $table) {
                $table->dropColumn('driver_name');
            });
        }

        if (!$this->indexExists('vehicle_driver_payments', 'vehicle_driver_payments_vehicle_monthly_log_id_unique')) {
            Schema::table('vehicle_driver_payments', function (Blueprint $table) {
                $table->unique('vehicle_monthly_log_id', 'vehicle_driver_payments_vehicle_monthly_log_id_unique');
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        return count(DB::select('SHOW INDEX FROM ' . $table . ' WHERE Key_name = ?', [$index])) > 0;
    }
};
