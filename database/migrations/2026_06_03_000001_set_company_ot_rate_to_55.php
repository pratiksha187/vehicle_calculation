<?php

use App\Models\Vehicle;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('vehicles')->update([
            'ot_rate_per_hour' => Vehicle::DEFAULT_COMPANY_OT_RATE_PER_HOUR,
        ]);
    }

    public function down(): void
    {
        DB::table('vehicles')->update([
            'ot_rate_per_hour' => 0,
        ]);
    }
};
