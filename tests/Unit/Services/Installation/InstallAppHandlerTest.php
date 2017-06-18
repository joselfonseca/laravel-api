<?php

namespace Tests\Unit\Services\Installation;

use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class InstallAppHandlerTest extends TestCase
{

    use DatabaseMigrations;

    function makeHandler()
    {
        return  app(\App\Services\Installation\InstallAppHandler::class);
    }

    function test_it_creates_roles()
    {
        $handler = $this->makeHandler();
        $handler->createRoles();
        $this->assertDatabaseHas('roles', [
            'name' => 'Administrator'
        ]);
    }

    function test_it_creates_permissions()
    {
        $handler = $this->makeHandler();
        $handler->createPermissions();
        $this->assertDatabaseHas('permissions', [
            'name' => 'List users'
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'Delete users'
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'Update roles'
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'List permissions'
        ]);
    }

    function test_it_creates_admin_user()
    {
        $handler = $this->makeHandler();
        $handler->createAdminUser([
            'name' => 'Jose Fonseca',
            'email' => 'jose@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'Jose Fonseca',
            'email' => 'jose@example.com',
        ]);
        $user = \App\Entities\User::where('email', 'jose@example.com')->first();
        $this->assertNotNull($user->uuid);
    }

    function test_it_validates_input_for_creating_user()
    {
        $handler = $this->makeHandler();
        try{
            $handler->createAdminUser([
                'name' => 'Jose Fonseca',
            ]);
            $this->fail('Validation to create user did not run.');
        } catch (ValidationException $e) {
            $this->assertDatabaseMissing('users', [
                'name' => 'Jose Fonseca',
                'email' => 'jose@example.com',
            ]);
        }
    }

    function test_it_assigns_admin_role_to_admin_user()
    {
        $handler = $this->makeHandler();
        $result = $handler->createRoles()->createPermissions()->createAdminUser([
            'name' => 'Jose Fonseca',
            'email' => 'jose@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ])->assignAdminRoleToAdminUser();
        $this->assertTrue($result->adminUser->hasRole('Administrator'));
    }

    function test_it_assigns_all_permissions_to_admin_role()
    {
        $handler = $this->makeHandler();
        $result = $handler->createRoles()->createPermissions()->assignAllPermissionsToAdminRole();
        $role = $result->roles->first();
        $this->assertTrue($role->hasPermissionTo('List users'));
        $this->assertTrue($role->hasPermissionTo('Delete users'));
        $this->assertTrue($role->hasPermissionTo('List permissions'));
    }

}