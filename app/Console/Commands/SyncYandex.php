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
 * @copyright  2018-2019
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
     *
     * @throws \SoapFault
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
            if ($records = $this->fillDrebedengiRecords($ya_account, $dd_account, $transactions->operations)) {
                $dd_client = new Client($dd_account->auth->email, $dd_account->auth->password);
                $dd_client->setRecordList($records);
                //\print_r($result);die;
            }
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
     * @param AccountYandex     $ya           Yandex account.
     * @param AccountDrebedengi $dd           Drebedengi account.
     * @param \stdClass[]       $transactions Yandex transactions.
     *
     * @return array
     */
    private function fillDrebedengiRecords(AccountYandex $ya, AccountDrebedengi $dd, array $transactions): array
    {
        $records = [];
        /** @noinspection ForeachSourceInspection */
        foreach ($transactions as $transaction) {
            if ($transaction->direction === AccountYandex::TYPE_IN || $transaction->title === 'Лукойл (ЮГ)') {
                continue;
            }
            $operation_type = $this->getOperation($transaction);
            $sum = $transaction->amount * 100;
            $records[] = [
                'client_id'        => $transaction->operation_id,
                'client_move_id'   => $operation_type === AccountDrebedengi::TYPE_MOVE ? $transaction->operation_id : null,
                'operation_date'   => Carbon::parse($transaction->datetime)->addRealHour(3)->format('Y-m-d H:i:s'),
                'operation_type'   => $operation_type,
                'sum'              => $operation_type === AccountDrebedengi::TYPE_MOVE ? -$sum : $sum,
                'currency_id'      => $dd->data->currencies->RUB,
                'place_id'         => $this->getPlace($ya, $dd, $operation_type),
                'budget_object_id' => $this->getBudget($ya, $dd, $transaction, $operation_type),
                'comment'          => $this->getComment($transaction),
                'is_duty'          => false
            ];
        }
        //\print_r($transactions); \print_r($records);die;

        return $records;
    }

    /**
     * Get operation.
     *
     * @param \stdClass $transaction Yandex transaction.
     *
     * @return integer
     */
    private function getOperation(\stdClass $transaction): int
    {
        return $transaction->direction === AccountYandex::TYPE_OUT
            ? AccountDrebedengi::TYPE_WASTE
            : AccountDrebedengi::TYPE_MOVE;
    }

    /**
     * Get place.
     *
     * @param AccountYandex     $ya            Yandex account.
     * @param AccountDrebedengi $dd            Drebedengi account.
     * @param integer           $operationType Operation.
     *
     * @return integer
     */
    private function getPlace(AccountYandex $ya, AccountDrebedengi $dd, int $operationType): int
    {
        return $operationType === AccountDrebedengi::TYPE_MOVE ? $dd->getPlaceAlfaRub() : $dd->getPlaceYandex($ya->title);
    }

    /**
     * Get source.
     *
     * @param AccountYandex     $ya            Yandex account.
     * @param AccountDrebedengi $dd            Drebedengi account.
     * @param \stdClass         $transaction   Yandex transaction.
     * @param integer           $operationType Operation.
     *
     * @return integer
     */
    private function getBudget(AccountYandex $ya, AccountDrebedengi $dd, \stdClass $transaction, int $operationType): int
    {
        $budget = 0;
        switch ($operationType) {
            case AccountDrebedengi::TYPE_WASTE:
                if (\strpos($transaction->title, 'APTEKA') !== false) {
                    $budget = $dd->getCategoryMeds();
                } elseif (\preg_match('/LUKOIL|ROSNEFT/', $transaction->title)) {
                    $budget = $dd->getCategoryFuel();
                } elseif (\preg_match('/IL PATIO|IP CYBULINA/', $transaction->title)) {
                    $budget = $dd->getCategoryFastFood();
                } elseif (\strpos($transaction->title, 'SIS.NESK.RU') !== false) {
                    $budget = $dd->getCategoryPower();
                } elseif (\strpos($transaction->title, 'AVTODETALI') !== false) {
                    $budget = $dd->getCategoryCarParts();
                } elseif (\strpos($transaction->title, 'DNS') !== false) {
                    $budget = $dd->getCategoryAppliances();
                } elseif (\strpos($transaction->title, 'PLATA ZA PROEZD') !== false) {
                    $budget = $dd->getCategoryFare();
                } elseif (\preg_match('/BAUCENTER|VSEINSTRUMENTY|LERUA/', $transaction->title)) {
                    $budget = $dd->getCategoryHomeEquipment();
                } elseif (\strpos($transaction->title, 'tdgorizont') !== false) {
                    $budget = $dd->getCategoryHomeBills();
                } elseif (\strpos($transaction->title, 'GBUZ') !== false) {
                    $budget = $dd->getCategoryMedTreatment();
                } else {
                    $budget = $dd->getCategoryFood();
                }
                break;
            case AccountDrebedengi::TYPE_MOVE:
            case AccountDrebedengi::TYPE_CHANGE:
                $budget = $this->getPlace($ya, $dd, AccountDrebedengi::TYPE_INCOME);
                break;
            case AccountDrebedengi::TYPE_INCOME:
            default:
                break;
        }

        return $budget;
    }

    /**
     * Get comment.
     *
     * @param \stdClass $transaction Yandex transaction.
     *
     * @return string
     */
    private function getComment(\stdClass $transaction): string
    {
        if (\strpos($transaction->title, 'SIS.NESK.RU') !== false) {
            $comment = '[Электроэнергия]';
        } elseif (\strpos($transaction->title, 'tdgorizont') !== false) {
            $comment = '[Участок]';
        } else {
            $comment = 'balance-server';
        }

        return $comment;
    }
}
