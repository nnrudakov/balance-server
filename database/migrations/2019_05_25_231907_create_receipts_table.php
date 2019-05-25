<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Receipts table migration.
 *
 * @package    Balance
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018-2019
 */
class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('receipts', static function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->string('hash', 32)->comment('Data hash');
            $table->json('data')->comment('Data');
            $table->json('response')->comment('Response');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
}
