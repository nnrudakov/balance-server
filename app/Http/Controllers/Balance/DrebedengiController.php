<?php

declare(strict_types=1);

namespace App\Http\Controllers\Balance;

use Illuminate\Http\{Request, Response};
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Balance\Account;
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
     * Account name.
     *
     * @var string
     */
    private const NAME = 'drebedengi';
    /**
     * Account title.
     *
     * @var string
     */
    private const TITLE = 'Дребеденьги';

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
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     *
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        //ONTxz5eB
        $auth = (object) $request->only('email', 'password');
        $account = new Account([
            'user_id' => \Auth::user()->id,
            'name' => static::NAME,
            'title' => static::TITLE,
            'auth' => $auth
        ]);
        $client = new Client($auth->email, $auth->password);
        if ($client->getBalance()) {
            $account->saveOrFail();
        }

        return \redirect('/drebedengi/show');
    }

    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function show(): View
    {
        /** @var Account $account */
        $account = Account::where('name', static::NAME)->first();
        $client = new Client($account->auth->email, $account->auth->password);
        $currencies = \array_pluck($client->getCurrencyList(), 'code', 'id');
        $balances = $client->getBalance();
        $fmt = numfmt_create( 'ru_RU', \NumberFormatter::CURRENCY);
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
     * Show the form for editing the specified resource.
     *
     * @param  Account  $account
     * @return Response
     */
    public function edit(Account $account): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Account  $account
     * @return Response
     */
    public function update(Request $request, Account $account): Response
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Account  $account
     * @return Response
     */
    public function destroy(Account $account): Response
    {
        //
    }
}
