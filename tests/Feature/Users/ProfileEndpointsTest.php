<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ProfileEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }

    public function test_it_gets_user_profile()
    {
        Passport::actingAs(User::first());
        $response = $this->json('GET', 'api/me');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name' => 'Jose Fonseca',
                'email' => 'jose@example.com',
                'roles' => [],
            ],
        ]);
    }

    public function test_it_can_update_logged_user_profile_all_entity()
    {
        Passport::actingAs(User::first());
        $response = $this->json('PUT', '/api/me', [
            'name' => 'Jose Fonseca Edited',
            'email' => 'jose@example.com',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'name' => 'Jose Fonseca Edited',
            'email' => 'jose@example.com',
        ]);
        $response->assertJson([
            'data' => [
                'name' => 'Jose Fonseca Edited',
                'email' => 'jose@example.com',
                'roles' => [],
            ],
        ]);
    }

    public function test_it_can_update_profile_partial_entity()
    {
        Passport::actingAs(User::first());
        $response = $this->json('PATCH', '/api/me', [
            'name' => 'Jose Fonseca Edited',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'name' => 'Jose Fonseca Edited',
            'email' => 'jose@example.com',
        ]);
        $response->assertJson([
            'data' => [
                'name' => 'Jose Fonseca Edited',
                'email' => 'jose@example.com',
                'roles' => [],
            ],
        ]);
    }

    public function test_it_validates_input_for_update_profile()
    {
        Passport::actingAs(User::first());
        $response = $this->json('PATCH', '/api/me', [
            'name' => '',
        ]);
        $response->assertStatus(422);
    }

    public function test_it_validates_input_for_email_on_update_profile()
    {
        Passport::actingAs(User::first());
        $user = User::factory()->create();
        $response = $this->json('PATCH', '/api/me', [
            'email' => $user->email,
        ]);
        $response->assertStatus(422);
    }

    public function test_it_updates_logged_in_user_password()
    {
        $user = User::first();
        Passport::actingAs($user);
        $response = $this->json('PUT', '/api/me/password', [
            'current_password' => 'password',
            'password' => '123456789qq',
            'password_confirmation' => '123456789qq',
        ]);
        $response->assertStatus(200);
        $this->assertTrue(app(Hasher::class)->check('123456789qq', $user->fresh()->password));
    }

    public function test_it_validates_input_to_update_logged_in_user_password_giving_wrong_current_pass()
    {
        $user = User::first();
        Passport::actingAs($user);
        $response = $this->json('PUT', '/api/me/password', [
            'current_password' => 'secret1234345',
            'password' => '123456789qq',
            'password_confirmation' => '123456789qq',
        ]);
        $response->assertStatus(422);
        $this->assertFalse(app(Hasher::class)->check('123456789qq', $user->fresh()->password));
    }

    public function test_it_validates_input_to_update_logged_in_user_password()
    {
        $user = User::first();
        Passport::actingAs($user);
        $response = $this->json('PUT', '/api/me/password', [
            'current_password' => 'secret1234',
            'password' => '12345',
            'password_confirmation' => '123456789qq',
        ]);
        $response->assertStatus(422);
        $this->assertFalse(app(Hasher::class)->check('123456789qq', $user->fresh()->password));
    }
}
