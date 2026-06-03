<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicle_daily_entries', function (Blueprint $table) {
            $table->string('driver_name')->nullable()->after('day');
        });

        DB::table('vehicle_daily_entries')
            ->whereNull('driver_name')
            ->update(['driver_name' => 'Rohit']);
    }

    public function down(): void
    {
        Schema::table('vehicle_daily_entries', function (Blueprint $table) {
            $table->dropColumn('driver_name');
        });
    }
};
