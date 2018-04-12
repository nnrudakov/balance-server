<?php

declare(strict_types=1);

namespace App\Balance;

/**
 * Yandex.Money account model.
 *
 * @package    App\Balance
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018
 */
class AccountYandex extends Account
{
    /**
     * Account name.
     *
     * @var string
     */
    public const NAME = 'yandex';
    /**
     * Account title.
     *
     * @var string
     */
    public const TITLE = 'Яндекс.Деньги';
    /**
     * Operation type `in`.
     *
     * @var string
     */
    public const TYPE_IN = 'in';
    /**
     * Operation type `out`.
     *
     * @var string
     */
    public const TYPE_OUT = 'out';

    protected $table = 'accounts';

    public static function query()
    {
        return parent::query()->where('name', static::NAME);
    }
}
