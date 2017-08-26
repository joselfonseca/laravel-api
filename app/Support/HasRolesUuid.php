<?php

namespace App\Support;

use App\Entities\Role as RoleEntity;
use Spatie\Permission\Contracts\Role;

trait HasRolesUuid
{
    /**
     * @param $role
     *
     * @return Role
     */
    protected function getStoredRole($role): Role
    {
        if (is_string($role)) {
            return app(RoleEntity::class)->where('name', $role)->orWhere('uuid', $role)->first();
        }

        return $role;
    }
}
