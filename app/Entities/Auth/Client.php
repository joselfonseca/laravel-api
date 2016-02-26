<?php

namespace App\Entities\Auth;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Client
 *
 * @package App\Entities\Auth
 */
class Client extends Model
{

    /**
     * @var string
     */
    public $table = "oauth_clients";

}
