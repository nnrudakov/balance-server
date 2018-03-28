<?php

declare(strict_types=1);

namespace App\Http\Controllers\Balance;

use Illuminate\View\View;
use App\Balance\Account;
use App\Http\Controllers\Controller;

/**
 * Accounts controller.
 *
 * @package    App\Http\Controllers\Balance
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018
 */
class AccountController extends Controller
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
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        return \view('balance.accounts', ['accounts' => Account::all()]);
    }
}
