<?php

namespace App\Entities\Users;

use Zizaco\Entrust\EntrustPermission;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends EntrustPermission
{

    use SoftDeletes;
}
