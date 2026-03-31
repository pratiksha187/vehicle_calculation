<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicle_daily_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_monthly_log_id')->constrained()->cascadeOnDelete();

            $table->date('entry_date');
            $table->string('day', 20)->nullable();
            $table->string('challan_no')->nullable();

            $table->decimal('diesel_added', 12, 2)->default(0);

            $table->integer('start_reading')->default(0);
            $table->integer('end_reading')->default(0);
            $table->integer('total_km')->default(0);

            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();

            $table->integer('total_minutes')->default(0);
            $table->integer('ot_minutes')->default(0);

            $table->text('remark')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_daily_entries');
    }
};