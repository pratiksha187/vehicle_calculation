<?php

use App\Models\VehicleDriverPayment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('vehicle_driver_payments', 'monthly_payment')) {
            Schema::table('vehicle_driver_payments', function (Blueprint $table) {
                $table->decimal('monthly_payment', 12, 2)
                    ->default(VehicleDriverPayment::DEFAULT_FIXED_PAYMENT)
                    ->after('driver_name');
            });
        }

        DB::table('vehicle_driver_payments')
            ->whereNull('monthly_payment')
            ->orWhere('monthly_payment', 0)
            ->update(['monthly_payment' => VehicleDriverPayment::DEFAULT_FIXED_PAYMENT]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('vehicle_driver_payments', 'monthly_payment')) {
            Schema::table('vehicle_driver_payments', function (Blueprint $table) {
                $table->dropColumn('monthly_payment');
            });
        }
    }
};
