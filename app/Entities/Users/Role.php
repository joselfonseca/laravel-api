<?php

namespace App\Entities\Users;

use Zizaco\Entrust\EntrustRole;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends EntrustRole
{

    use SoftDeletes;
}
