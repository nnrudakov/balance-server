<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Add data to accounts.
 *
 * @package    Balance
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018
 */
class AddDataField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        \Schema::table('accounts', function (Blueprint $table) {
            $table->jsonb('data')->nullable()->comment('Data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        \Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('data');
        });
    }
}
