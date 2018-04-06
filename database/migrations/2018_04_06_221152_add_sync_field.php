<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSyncField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('accounts', function (Blueprint $table) {
            $table->dateTime('sync_date')->nullable()->comment('Last sync date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('sync_date');
        });
    }
}
