<?php

namespace App\Entities\Users;

use Zizaco\Entrust\EntrustRole;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Role
 *
 * @package App\Entities\Users
 */
class Role extends EntrustRole
{

    use SoftDeletes;
}
