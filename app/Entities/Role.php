<?php

namespace App\Entities;

use App\Support\HasPermissionsUuid;
use Joselfonseca\LaravelApiTools\Traits\UuidScopeTrait;

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
