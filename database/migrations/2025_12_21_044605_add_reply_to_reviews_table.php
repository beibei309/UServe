<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('h2u_reviews', function (Blueprint $table) {
        $table->text('hr_reply')->nullable()->after('hr_comment');
        $table->timestamp('hr_replied_at')->nullable()->after('hr_reply');
    });
}

public function down()
{
    Schema::table('h2u_reviews', function (Blueprint $table) {
        $table->dropColumn(['hr_reply', 'hr_replied_at']);
    });
}
};
