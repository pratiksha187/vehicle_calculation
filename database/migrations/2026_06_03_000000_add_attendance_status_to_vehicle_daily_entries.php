<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicle_daily_entries', function (Blueprint $table) {
            $table->string('attendance_status', 20)->default('present')->after('driver_name');
        });

        DB::table('vehicle_daily_entries')
            ->whereNull('attendance_status')
            ->update(['attendance_status' => 'present']);
    }

    public function down(): void
    {
        Schema::table('vehicle_daily_entries', function (Blueprint $table) {
            $table->dropColumn('attendance_status');
        });
    }
};
