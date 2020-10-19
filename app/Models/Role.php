<?php

namespace App\Models;

use App\Support\HasPermissionsUuid;
use App\Support\UuidScopeTrait;

/**
 * Class Role.
 */
class Role extends \Spatie\Permission\Models\Role
{
    use UuidScopeTrait, HasPermissionsUuid;

    /**
     * @var array
     */
    protected $fillable = ['name', 'uuid', 'guard_name'];
}
