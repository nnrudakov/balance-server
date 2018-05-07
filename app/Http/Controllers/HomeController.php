<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Balance\Account;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        $accounts = [];
        foreach (Account::all() as $account) {
            $accounts[$account->title] = $account->getBalances();
        }

        return view('home', ['accounts' => $accounts]);
    }
}
