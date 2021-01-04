<?php

namespace Database\Seeders\Users;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * @var array|\Illuminate\Support\Collection
     */
    public $roles = [
        ['name' => 'Administrator'],
        ['name' => 'User'],
    ];

    /**
     * @var array|\Illuminate\Support\Collection
     */
    public $permissions = [
        'users' => [
            ['name' => 'List users'],
            ['name' => 'Create users'],
            ['name' => 'Delete users'],
            ['name' => 'Update users'],
        ],
        'roles' => [
            ['name' => 'List roles'],
            ['name' => 'Create roles'],
            ['name' => 'Delete roles'],
            ['name' => 'Update roles'],
        ],
        'permissions' => [
            ['name' => 'List permissions'],
        ],
    ];

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $this->createRoles()->createPermissions()->assignAllPermissionsToAdminRole();
    }

    /**
     * @return $this
     */
    public function createRoles()
    {
        $this->roles = collect($this->roles)->map(function ($role) {
            return Role::create($role);
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function createPermissions()
    {
        $this->permissions = collect($this->permissions)->map(function ($group) {
            return collect($group)->map(function ($permission) {
                return Permission::create($permission);
            });
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function assignAllPermissionsToAdminRole()
    {
        $role = Role::where('name', 'Administrator')->firstOrFail();
        $this->permissions->flatten()->each(function ($permission) use ($role) {
            $role->givePermissionTo($permission);
        });

        return $this;
    }
}
