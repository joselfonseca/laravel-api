<?php

namespace App\Support;

use Spatie\Permission\Contracts\Permission;
use App\Entities\Permission as PermissionEntity;

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
    protected function getStoredPermission($permissions): Permission
    {
        if (is_string($permissions)) {
            return app(PermissionEntity::class)->where('name', $permissions)->orWhere('uuid', $permissions)->first();
        }

        if (is_array($permissions)) {
            return app(PermissionEntity::class)->whereIn('name', $permissions)->orWhereIn('uuid', $permissions)->get();
        }

        return $permissions;
    }
}
