<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Accounts table migration.
 *
 * @package    Balance
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018
 */
class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        \Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->unsignedInteger('user_id')->comment('User ID');
            $table->string('name')->comment('Account name');
            $table->string('title')->comment('Account title');
            $table->json('auth')->comment('Authentication data');
            $table->timestamps();
            $table->unique(['user_id', 'name']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        \Schema::dropIfExists('accounts');
    }
}
