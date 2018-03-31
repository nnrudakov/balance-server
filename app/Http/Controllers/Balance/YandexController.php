<?php

declare(strict_types=1);

namespace App\Http\Controllers\Balance;

use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use YandexMoney\API;
use App\Http\Controllers\Controller;
use App\Balance\Account;

/**
 * Yandex.Money controller.
 *
 * @package    App\Http\Controllers\Balance
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018
 */
class YandexController extends Controller
{
    /**
     * Account name.
     *
     * @var string
     */
    private const NAME = 'yandex';
    /**
     * Account title.
     *
     * @var string
     */
    private const TITLE = 'Яндекс.Деньги';

    /**
     * Scopes.
     *
     * @var array|string
     */
    private static $scope = ['account-info', 'operation-history', 'operation-details'];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     *
     * @return View|Redirector|RedirectResponse
     *
     * @throws \ErrorException
     */
    public function create(Request $request)
    {
        $code = $request->get('code');
        if (!$code) {
            return \view('balance.yandex.create');
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $access_token_response = API::getAccessToken(\env('YANDEX_ID'), $code, \route('ya.add'), \env('YANDEX_SECRET'));
        if(property_exists($access_token_response, 'error')) {
            throw new \ErrorException($access_token_response->error);
        }
        $account = new Account([
            'user_id' => \Auth::user()->id,
            'name' => static::NAME,
            'title' => static::TITLE,
            'auth' => (object) ['access_token' => $access_token_response->access_token]
        ]);
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        /** @noinspection PhpUnhandledExceptionInspection */
        $account->saveOrFail();

        return \redirect('/yandex/show');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Redirector|RedirectResponse
     *
     * @throws \Throwable
     */
    public function store()
    {
        $auth_url = API::buildObtainTokenUrl(\env('YANDEX_ID'), \route('ya.add'), static::$scope);

        return \redirect($auth_url);
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function show(): View
    {
        /** @var Account $account */
        /** @noinspection PhpUndefinedMethodInspection */
        $account = Account::where('name', static::NAME)->first();
        $client = new API($account->auth->access_token);
        $fmt = numfmt_create( 'ru_RU', \NumberFormatter::CURRENCY);
        /** @noinspection PhpUnhandledExceptionInspection */
        $accountInfo = $client->accountInfo();
        $accountInfo->formatted = numfmt_format_currency($fmt, $accountInfo->balance, 'RUB');

        return \view('balance.yandex.show', ['balance' => $accountInfo]);
    }
}
