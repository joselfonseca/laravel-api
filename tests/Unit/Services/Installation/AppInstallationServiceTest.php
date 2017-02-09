<?php

namespace Tests\Unit\Services\Installation;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;

class AppInstallationServiceTest extends TestCase
{

    use DatabaseMigrations;

    protected function makeService()
    {
        return app(\App\Services\Installation\AppInstallationService::class);
    }

    function test_it_installs_the_app()
    {
        $service = $this->makeService();
        $service->installApp([
            'name' => 'Jose Fonseca',
            'email' => 'jose@example.com',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'Jose Fonseca',
            'email' => 'jose@example.com',
        ]);
        $user = \App\Entities\User::where('name', 'Jose Fonseca')->first();
        $this->assertTrue($user->hasRole('Administrator'));
        $this->assertTrue($user->hasPermissionTo('Update roles'));
    }

}