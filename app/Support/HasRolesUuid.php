<?php

namespace App\Support;

use App\Entities\Role;

trait HasRolesUuid
{
    /**
     * @param $role
     *
     * @return Role
     */
    protected function getStoredRole($role)
    {
        if (is_string($role)) {
            return app(Role::class)->where('name', $role)->orWhere('uuid', $role)->first();
        }

        return $role;
    }
}
