<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_name');
            $table->string('vehicle_number')->unique();
            $table->string('owner_name')->nullable();
            $table->decimal('fixed_monthly_amount', 12, 2)->default(0);
            $table->decimal('ot_rate_per_hour', 12, 2)->default(0);
            $table->decimal('tds_percent', 5, 2)->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};