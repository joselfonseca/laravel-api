<?php

namespace Tests\Unit\Entities;

use Tests\TestCase;
use App\Entities\Role;
use App\Entities\Permission;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RoleTest extends TestCase
{

    use DatabaseMigrations;

    function test_it_syncs_permissions_by_object_collection()
    {
        $role = factory(Role::class)->create();
        $permissions = factory(Permission::class, 3)->create();
        $role->syncPermissions($permissions);
        $permissions->each(function($permission) use ($role) {
            $this->assertDatabaseHas('role_has_permissions', [
                'role_id' => $role->id,
                'permission_id' => $permission->id
            ]);
        });
    }

    function test_it_syncs_permissions_by_array_of_names()
    {
        $role = factory(Role::class)->create();
        $permissions = factory(Permission::class, 3)->create();
        $role->syncPermissions($permissions->pluck('name')->toArray());
        $permissions->each(function($permission) use ($role) {
            $this->assertDatabaseHas('role_has_permissions', [
                'role_id' => $role->id,
                'permission_id' => $permission->id
            ]);
        });
    }

    function test_it_syncs_permissions_by_array_of_uuids()
    {
        $role = factory(Role::class)->create();
        $permissions = factory(Permission::class, 3)->create();
        $role->syncPermissions($permissions->pluck('uuid')->toArray());
        $permissions->each(function($permission) use ($role) {
            $this->assertDatabaseHas('role_has_permissions', [
                'role_id' => $role->id,
                'permission_id' => $permission->id
            ]);
        });
    }

}