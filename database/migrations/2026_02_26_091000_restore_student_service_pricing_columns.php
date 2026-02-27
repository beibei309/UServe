<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('h2u_student_services', function (Blueprint $table) {
            if (!Schema::hasColumn('h2u_student_services', 'hss_suggested_price')) {
                $table->decimal('hss_suggested_price', 10, 2)->nullable()->after('hss_description');
            }

            if (!Schema::hasColumn('h2u_student_services', 'hss_price_range')) {
                $table->string('hss_price_range')->nullable()->after('hss_status');
            }
        });

        // Optional backfill to reduce null values in existing rows.
        DB::statement("UPDATE h2u_student_services SET hss_suggested_price = COALESCE(hss_suggested_price, hss_standard_price)");
    }

    public function down(): void
    {
        Schema::table('h2u_student_services', function (Blueprint $table) {
            $drop = [];
            if (Schema::hasColumn('h2u_student_services', 'hss_suggested_price')) {
                $drop[] = 'hss_suggested_price';
            }
            if (Schema::hasColumn('h2u_student_services', 'hss_price_range')) {
                $drop[] = 'hss_price_range';
            }
            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
