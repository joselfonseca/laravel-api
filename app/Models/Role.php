<?php

namespace App\Models;

use App\Support\HasPermissionsUuid;
use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Role.
 */
class Role extends \Spatie\Permission\Models\Role
{
    use UuidScopeTrait, HasPermissionsUuid, HasFactory;

    /**
     * @var array
     */
    protected $fillable = ['name', 'uuid', 'guard_name'];
}
