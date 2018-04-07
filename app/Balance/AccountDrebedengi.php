<?php

declare(strict_types=1);

namespace App\Balance;

/**
 * Drebedengi account model.
 *
 * @package    App\Balance
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018
 */
class AccountDrebedengi extends Account
{
    /**
     * Account name.
     *
     * @var string
     */
    public const NAME = 'drebedengi';
    /**
     * Account title.
     *
     * @var string
     */
    public const TITLE = 'Дребеденьги';

    protected $table = 'accounts';

    public static function query()
    {
        return parent::query()->where('name', static::NAME);
    }
}
