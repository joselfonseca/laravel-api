<?php

namespace App\Entities\Users;

use Zizaco\Entrust\EntrustPermission;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Permission
 * @package App\Entities\Users
 */
class Permission extends EntrustPermission
{

    use SoftDeletes;
}
