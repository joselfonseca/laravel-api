<?php

namespace Tests\Unit\Entities;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_syncs_permissions_by_object_collection()
    {
        $role = Role::factory()->create();
        $permissions = Permission::factory()->count(3)->create();
        $role->syncPermissions($permissions);
        $permissions->each(function ($permission) use ($role) {
            $this->assertDatabaseHas('role_has_permissions', [
                'role_id' => $role->id,
                'permission_id' => $permission->id,
            ]);
        });
    }

    public function test_it_syncs_permissions_by_array_of_names()
    {
        $role = Role::factory()->create();
        $permissions = Permission::factory()->count(3)->create();
        $role->syncPermissions($permissions->pluck('name')->toArray());
        $permissions->each(function ($permission) use ($role) {
            $this->assertDatabaseHas('role_has_permissions', [
                'role_id' => $role->id,
                'permission_id' => $permission->id,
            ]);
        });
    }

    public function test_it_syncs_permissions_by_array_of_uuids()
    {
        $role = Role::factory()->create();
        $permissions = Permission::factory()->count(3)->create();
        $role->syncPermissions($permissions->pluck('uuid')->toArray());
        $permissions->each(function ($permission) use ($role) {
            $this->assertDatabaseHas('role_has_permissions', [
                'role_id' => $role->id,
                'permission_id' => $permission->id,
            ]);
        });
    }

    public function test_it_can_fill_uuid_at_creation()
    {
        $uuid = '84e28c10-8991-11e7-ad89-056674746d73';

        $roleNotFilled = Role::factory()->create();
        $this->assertNotEquals($uuid, $roleNotFilled->uuid);

        $roleFilled = Role::factory()->create(['uuid' => $uuid]);
        $this->assertEquals($uuid, $roleFilled->uuid);
    }
}
