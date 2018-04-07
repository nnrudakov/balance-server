<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use Carbon\Carbon;
use YandexMoney\API;
use App\Balance\{AccountYandex, AccountDrebedengi};
use App\Balance\Drebedengi\Client;
use App\Exceptions\YandexException;

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
        $ya_accounts = AccountYandex::query()->get();

        foreach ($ya_accounts as $ya_account) {
            try {
                $transactions = $this->getYandexTransactions($ya_account);
            } catch (YandexException $e) {
                $this->error("\t" . $e->getMessage());
                continue;
            }

            $dd_account = AccountDrebedengi::query()->where('user_id', $ya_account->user_id)->first();
            $records = $this->fillDrebedengiRecords($dd_account, $transactions->operations);
            $dd_client = new Client($dd_account->auth->email, $dd_account->auth->password);
            $dd_client->setRecordList($records);
            $ya_account->sync_date = Carbon::now();
            $ya_account->update(['sync_date']);
        }

        $this->after();
    }

    /**
     * Get yandex transactions.
     *
     * @param AccountYandex $account
     *
     * @return \stdClass
     *
     * @throws YandexException
     */
    private function getYandexTransactions(AccountYandex $account): \stdClass
    {
        $client = new API($account->auth->access_token);
        $tz = new \DateTimeZone('Europe/Moscow');
        $last_sync = $account->sync_date ? Carbon::parse($account->sync_date, $tz) : Carbon::now($tz);
        /** @var \stdClass $transactions */
        $transactions = $client->operationHistory(['from' => $last_sync->toRfc3339String()]);
        if (!empty($transactions->error)) {
            throw new YandexException('Error: ' . $transactions->error);
        }

        return $transactions;
    }

    /**
     * Fill records for Drebedengi.
     *
     * @param AccountDrebedengi $account      Drebedengi account.
     * @param \stdClass[]       $transactions Yandex transactions.
     *
     * @return array
     */
    private function fillDrebedengiRecords(AccountDrebedengi $account, array $transactions): array
    {
        $records = [];
        /** @noinspection ForeachSourceInspection */
        foreach ($transactions as $transaction) {
            $records[] = [
                'operation_date' => Carbon::parse($transaction->datetime)->addRealHour(3)->format('Y-m-d H:i:s'),
                'sum' => $transaction->amount * 100,
                'currency_id' => $account->data->currencies->RUB,
                'place_id' => 0,
                'operation_type' => 0
            ];
        }
        \print_r($transactions);
        \print_r($records);
        die;

        return $records;
    }
}
