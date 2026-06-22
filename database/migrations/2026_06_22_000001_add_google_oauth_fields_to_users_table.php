<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            if (! Schema::hasColumn('h2u_users', 'hu_google_id')) {
                $table->string('hu_google_id')->nullable()->unique()->after('hu_email_verified_at');
            }

            if (! Schema::hasColumn('h2u_users', 'hu_google_avatar')) {
                $table->string('hu_google_avatar')->nullable()->after('hu_google_id');
            }

            if (! Schema::hasColumn('h2u_users', 'hu_auth_provider')) {
                $table->string('hu_auth_provider', 40)->nullable()->after('hu_google_avatar');
            }

            if (! Schema::hasColumn('h2u_users', 'hu_terms_accepted_at')) {
                $table->timestamp('hu_terms_accepted_at')->nullable()->after('hu_auth_provider');
            }
        });
    }

    public function down(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('h2u_users', 'hu_terms_accepted_at') ? 'hu_terms_accepted_at' : null,
                Schema::hasColumn('h2u_users', 'hu_auth_provider') ? 'hu_auth_provider' : null,
                Schema::hasColumn('h2u_users', 'hu_google_avatar') ? 'hu_google_avatar' : null,
                Schema::hasColumn('h2u_users', 'hu_google_id') ? 'hu_google_id' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
