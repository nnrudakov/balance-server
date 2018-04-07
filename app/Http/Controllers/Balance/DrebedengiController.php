<?php

declare(strict_types=1);

namespace App\Http\Controllers\Balance;

use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Balance\AccountDrebedengi;
use App\Balance\Drebedengi\Client;

/**
 * Drebedengi controller.
 *
 * @package    App\Http\Controllers\Balance
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018
 */
class DrebedengiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return \view('balance.drebedengi.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Redirector|RedirectResponse
     *
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        $auth = (object) $request->only('email', 'password');
        $account = new AccountDrebedengi([
            'user_id' => \Auth::user()->id,
            'name' => AccountDrebedengi::NAME,
            'title' => AccountDrebedengi::TITLE,
            'auth' => $auth
        ]);
        $client = new Client($auth->email, $auth->password);
        if ($client->getBalance()) {
            $account->saveOrFail();
        }

        return \redirect(\route('dd.show'));
    }

    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function show(): View
    {
        $account    = AccountDrebedengi::query()->where('user_id', \Auth::user()->id)->first();
        $client     = new Client($account->auth->email, $account->auth->password);
        $currencies = \array_pluck($client->getCurrencyList(), 'code', 'id');
        $balances   = $client->getBalance();
        $fmt        = numfmt_create( 'ru_RU', \NumberFormatter::CURRENCY);
        foreach ($balances as &$balance) {
            $balance['formatted'] = numfmt_format_currency(
                $fmt,
                $balance['sum'] / 100,
                $currencies[$balance['currency_id']]
            );
        }

        return \view('balance.drebedengi.show', ['balances' => $balances]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Redirector|RedirectResponse
     */
    public function update()
    {
        $account = AccountDrebedengi::query()->where('user_id', \Auth::user()->id)->first();
        $client = new Client($account->auth->email, $account->auth->password);
        $account->data = [
            'currencies' => \array_flip(\array_pluck($client->getCurrencyList(), 'code', 'id')),
            'categories' => $client->getCategoryList(),
            'sources' => $client->getSourceList(),
            'places' => $client->getPlaceList()
        ];
        $account->update(['data']);

        return \redirect(\route('dd.show'));
    }
}
