<?php

declare(strict_types=1);

namespace App\Balance;

use Illuminate\Database\Eloquent\Model;

/**
 * Account model.
 *
 * @property integer $id         ID.
 * @property integer $user_id    User ID.
 * @property string  $name       Account name.
 * @property string  $title      Account title.
 * @property object  $auth       Authentication data.
 * @property string  $sync_date  Last synchronization date.
 * @property integer $created_at Created date.
 * @property integer $updated_at Updated date.
 *
 * @package    App\Balance
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018
 */
class Account extends Model
{
    protected $fillable = [
        'user_id', 'name', 'title', 'auth'
    ];

    protected $casts = [
        'auth' => 'object',
        'sync_date' => 'datetime'
    ];
}
