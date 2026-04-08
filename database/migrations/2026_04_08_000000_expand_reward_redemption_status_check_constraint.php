<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE h2u_reward_redemptions DROP CONSTRAINT IF EXISTS h2u_reward_redemptions_hrr_status_check');

        DB::statement("ALTER TABLE h2u_reward_redemptions
            ADD CONSTRAINT h2u_reward_redemptions_hrr_status_check
            CHECK (hrr_status IN ('pending', 'active', 'approved', 'rejected', 'used', 'expired', 'cancelled'))");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Best-effort normalization so the old constraint can be re-applied.
        DB::statement("UPDATE h2u_reward_redemptions SET hrr_status = 'active' WHERE hrr_status = 'approved'");
        DB::statement("UPDATE h2u_reward_redemptions SET hrr_status = 'cancelled' WHERE hrr_status = 'rejected'");

        DB::statement('ALTER TABLE h2u_reward_redemptions DROP CONSTRAINT IF EXISTS h2u_reward_redemptions_hrr_status_check');

        DB::statement("ALTER TABLE h2u_reward_redemptions
            ADD CONSTRAINT h2u_reward_redemptions_hrr_status_check
            CHECK (hrr_status IN ('pending', 'active', 'used', 'expired', 'cancelled'))");
    }
};
