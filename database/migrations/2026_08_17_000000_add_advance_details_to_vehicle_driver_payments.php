<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicle_driver_payments', function (Blueprint $table) {
            $table->date('advance_date')->nullable()->after('advance_payment');
            $table->string('advance_screenshot')->nullable()->after('advance_date');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_driver_payments', function (Blueprint $table) {
            $table->dropColumn(['advance_date', 'advance_screenshot']);
        });
    }
};
