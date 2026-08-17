<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('vehicle_driver_payment_advances');

        Schema::create('vehicle_driver_payment_advances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_driver_payment_id');
            $table->date('advance_date')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('screenshot')->nullable();
            $table->timestamps();

            $table->foreign('vehicle_driver_payment_id', 'vdp_advances_payment_id_fk')
                ->references('id')
                ->on('vehicle_driver_payments')
                ->cascadeOnDelete();
        });

        DB::table('vehicle_driver_payments')
            ->where('advance_payment', '>', 0)
            ->orderBy('id')
            ->chunkById(100, function ($payments) {
                foreach ($payments as $payment) {
                    DB::table('vehicle_driver_payment_advances')->insert([
                        'vehicle_driver_payment_id' => $payment->id,
                        'advance_date' => $payment->advance_date ?? null,
                        'amount' => $payment->advance_payment,
                        'screenshot' => $payment->advance_screenshot ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_driver_payment_advances');
    }
};
