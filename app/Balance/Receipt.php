<?php

declare(strict_types=1);

namespace App\Balance;

use Illuminate\Database\Eloquent\Model;

/**
 * Account model.
 *
 * @property integer   $id            ID.
 * @property string    $hash          User ID.
 * @property \stdClass $data          Account name.
 * @property \stdClass $response      Account title.
 * @property integer   $created_at    Created date.
 * @property integer   $updated_at    Updated date.
 *
 * @package    App\Balance
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018-2019
 */
class Receipt extends Model
{
    protected $fillable = ['hash', 'data', 'response'];
    protected $casts = ['data' => 'object', 'response' => 'object'];
}
