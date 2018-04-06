<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use Carbon\Carbon;
use YandexMoney\API;
use App\Balance\Account;
use App\Http\Controllers\Balance\YandexController;

/**
 * Command for synchronization `Yandex.Money` and `Drebedengi`.
 *
 * @package    App\Console\Commands
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018
 */
class SyncYandex extends Command
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->name        = 'yandex';
        $this->signature   = $this->name;
        $this->description = 'Store transactions from Yandex.Money to Drebedengi';
        parent::__construct();
    }

    /**
     * Command execution.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->before();
        /** @var Account $account */
        /** @noinspection PhpUndefinedMethodInspection */
        $account = Account::where('name', YandexController::NAME)->first();
        $client = new API($account->auth->access_token);
        $tz = new \DateTimeZone('Europe/Moscow');
        $last_sync = $account->sync_date ? Carbon::parse($account->sync_date, $tz) : Carbon::now($tz);

        $transactions = $client->operationHistory(['from' => $last_sync->toRfc3339String()]);
        if (!empty($transactions->error)) {
            $this->error("\tError: " . $transactions->error);
        } else {
            //\print_r($transactions);die;
            $account->sync_date = Carbon::now();
            $account->update(['sync_date']);
        }

        $this->after();
    }
}
