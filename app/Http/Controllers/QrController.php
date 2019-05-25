<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use kosov\fnscheck\{FnsCheckApi, FnsCheckApiException, FnsCheckAuth, FnsCheckHelper};
use kosov\fnscheck\request\CheckExistRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Balance\{AccountDrebedengi, Receipt};
use App\Balance\Drebedengi\Client;

/**
 * Receipt handler from Android application.
 *
 * @package    App\Http\Controllers\Balance
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018-2019
 *
 * @see https://toster.ru/q/442458
 * @see https://habr.com/post/358966/
 * @see https://github.com/eisaev/fns
 * @see https://github.com/kosov/fns-check
 */
class QrController extends Controller
{
    /**
     * Store receipt to `Drebedengi` account.
     *
     * @param Request $request Request.
     *
     * @return string
     */
    public function index(Request $request): string
    {
        //return 'OK';
        $data = $request->post();
        /*$data = [
            't'  => '20190515T220400',
            's'  => 2848.52,
            'fn' => 8716000100025853,
            'i'  => 1884,
            'fp' => 608840949,
            'n'  => 1
        ];*/
        \file_put_contents(__DIR__ . '/qr.log', \print_r($data, true));
        $qr = \http_build_query($data);
        try {
            $hash = $this->getHash($qr);
            $receipt_data = $this->getReceiptData($qr);
            $this->storeReceiptData($receipt_data);
            $this->storeReceipt($hash, $data, $receipt_data);
        } catch (\LogicException $e) {
            \Log::error('(Balance) ' . $e->getMessage());
            throw new HttpException(500, $e->getMessage(), $e);
        } catch (FnsCheckApiException $e) {
            \Log::error('(FNS) ' . $e->getMessage());
            throw new HttpException(500, $e->getMessage(), $e);
        } catch (\SoapFault $e) {
            \Log::error('(Drebedengi) ' . $e->getMessage());
            throw new HttpException(500, $e->getMessage(), $e);
        } catch (\Throwable $e) {
            \Log::error('(Balance) ' . $e->getMessage());
            throw new HttpException(500, $e->getMessage(), $e);
        }

        \Log::info('(Balance) Receipt `' . $hash . '` stored');

        return 'OK';
    }

    /**
     * Compose receipt hash string to exclude duplicates.
     *
     * @param string $qr QR code string.
     *
     * @return string
     *
     * @throws \LogicException
     */
    private function getHash(string $qr): string
    {
        $hash = \md5($qr);
        if (Receipt::query()->where('hash', $hash)->exists()) {
            throw new \LogicException('Receipt `' . $hash . '` already exists');
        }

        return $hash;
    }

    /**
     * Returns receipt full info.
     *
     * @param string $qr QR code string.
     *
     * @return \stdClass
     *
     * @throws FnsCheckApiException
     */
    private function getReceiptData(string $qr): \stdClass
    {
        $normalizedData = FnsCheckHelper::fromQRCode($qr);
        $auth = new FnsCheckAuth(\env('FNS_LOGIN'), \env('FNS_PASSWORD'));
        $fnsCheckApi = new FnsCheckApi();
        $response = $fnsCheckApi->call(new CheckExistRequest($normalizedData, $auth));
        if ($response->getHttpResponse()->getStatusCode() !== 204) {
            throw new FnsCheckApiException('Receipt not exists: ' . $response->getContents());
        }

        $fnsCheckApi->checkDetail($normalizedData, $auth);
        $receipt = $fnsCheckApi->checkDetail($normalizedData, $auth)->getContents();
        //\file_put_contents(__DIR__ . '/ch.json', $receipt);

        return \json_decode($receipt, false);
    }

    /**
     * Store receipt data to `Drebedengi` account.
     *
     * @param \stdClass $data Receipt data.
     *
     * @throws \SoapFault
     */
    private function storeReceiptData(\stdClass $data): void
    {
        $dd_account = AccountDrebedengi::query()->where('user_id', 1)->first();
        $doc = $data->document->receipt;
        $records = [[
            'client_id'        => (int) $doc->fiscalDocumentNumber . \time(),
            'client_move_id'   => null,
            'operation_date'   => Carbon::parse($doc->dateTime)->format('Y-m-d H:i:s'),
            'operation_type'   => AccountDrebedengi::TYPE_WASTE,
            'sum'              => $doc->totalSum,
            'currency_id'      => $dd_account->data->currencies->RUB,
            'place_id'         => $dd_account->getPlaceCash(),
            'budget_object_id' => $dd_account->getCategoryFood(),
            'comment'          => 'balance-app',
            'is_duty'          => false
        ]];
        $dd_client = new Client($dd_account->auth->email, $dd_account->auth->password);
        $dd_client->setRecordList($records);
    }

    /**
     * Store receipt hash.
     *
     * @param string    $hash    Hast string.
     * @param array     $data    Request data (QR code).
     * @param \stdClass $receipt Receipt data.
     *
     * @return void
     *
     * @throws \Throwable
     */
    private function storeReceipt(string $hash, array $data, \stdClass $receipt): void
    {
        (new Receipt(['hash' => $hash, 'data' => (object) $data, 'response' => $receipt]))->saveOrFail();
    }
}
