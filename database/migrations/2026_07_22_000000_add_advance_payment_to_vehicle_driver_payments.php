<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicle_driver_payments', function (Blueprint $table) {
            $table->decimal('advance_payment', 12, 2)->default(0)->after('ot_amount');
            $table->decimal('net_payment', 12, 2)->default(0)->after('total_payment');
        });

        DB::table('vehicle_driver_payments')->update([
            'net_payment' => DB::raw('total_payment - advance_payment'),
        ]);
    }

    public function down(): void
    {
        Schema::table('vehicle_driver_payments', function (Blueprint $table) {
            $table->dropColumn(['advance_payment', 'net_payment']);
        });
    }
};
