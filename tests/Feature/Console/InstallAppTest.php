<?php

namespace Tests\Feature\Console;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallAppTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_installs_app_with_command()
    {
        $this->artisan('app:install')
            ->expectsQuestion('What is the Admininstrator\'s name?', 'Taylor')
            ->expectsQuestion('What is the Admininstrator\'s email?', 'taylor@example.com')
            ->expectsQuestion('What is the Admininstrator\'s password?', '123456789');

        $this->assertDatabaseHas('roles', [
            'name' => 'Administrator'
        ]);
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
        $this->assertDatabaseHas('users', [
            'name' => 'Taylor',
            'email' => 'taylor@example.com',
        ]);
    }
}