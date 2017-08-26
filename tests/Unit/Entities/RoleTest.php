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

    function test_it_can_fill_uuid_at_creation()
    {
        $uuid = '84e28c10-8991-11e7-ad89-056674746d73';

        $roleNotFilled = factory(Role::class)->create();
        $this->assertNotEquals($uuid, $roleNotFilled->uuid);

        $roleFilled = factory(Role::class)->create(['uuid' => $uuid]);
        $this->assertEquals($uuid, $roleFilled->uuid);
    }
}