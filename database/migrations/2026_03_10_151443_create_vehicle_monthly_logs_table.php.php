<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicle_monthly_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();

            $table->date('from_date');
            $table->date('to_date');

            $table->integer('opening_reading')->default(0);
            $table->integer('closing_reading')->default(0);
            $table->integer('total_km')->default(0);

            $table->decimal('diesel_total', 12, 2)->default(0);
            $table->decimal('average_kmpl', 12, 4)->default(0);

            $table->integer('total_ot_minutes')->default(0);
            $table->decimal('total_ot_hours', 12, 2)->default(0);
            $table->decimal('total_ot_amount', 12, 2)->default(0);

            $table->decimal('fixed_monthly_amount', 12, 2)->default(0);
            $table->decimal('total_billing_amount', 12, 2)->default(0);
            $table->decimal('tds_percent', 5, 2)->default(1);
            $table->decimal('tds_amount', 12, 2)->default(0);
            $table->decimal('net_payable', 12, 2)->default(0);

            $table->timestamps();

            $table->unique(['vehicle_id', 'from_date', 'to_date'], 'vehicle_month_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_monthly_logs');
    }
};