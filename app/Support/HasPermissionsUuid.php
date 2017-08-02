<?php

namespace App\Support;

use App\Entities\Permission;

/**
 * Class HasPermissionsUuid.
 */
trait HasPermissionsUuid
{
    /**
     * Added support to use a UUID to find the Permission.
     *
     * @param string|array|Permission|\Illuminate\Support\Collection $permissions
     *
     * @return Permission
     */
    protected function getStoredPermission($permissions)
    {
        if (is_string($permissions)) {
            return app(Permission::class)->where('name', $permissions)->orWhere('uuid', $permissions)->first();
        }

        if (is_array($permissions)) {
            return app(Permission::class)->whereIn('name', $permissions)->orWhereIn('uuid', $permissions)->get();
        }

        return $permissions;
    }
}
