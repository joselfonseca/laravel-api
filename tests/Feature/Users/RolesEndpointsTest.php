<?php

namespace Tests\Feature\Users;

use App\Entities\Role;
use App\Entities\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RolesEndpointsTest extends TestCase
{

    use DatabaseMigrations;

    function setUp()
    {
        parent::setUp();
        $this->installApp();
    }


    public function test_it_list_roles()
    {
        factory(Role::class, 10)->create();
        Passport::actingAs(User::first());
        $response = $this->json('GET', 'api/roles');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['name' => 'Administrator']
            ],
            'meta' => [
                'pagination' => [
                    'total' => 11
                ]
            ]
        ]);
    }

    public function test_it_prevents_unauthorized_roles_listing()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $response = $this->json('GET', 'api/roles');
        $response->assertStatus(403);
    }

    public function test_it_can_create_a_role()
    {
        Passport::actingAs(User::first());
        $response = $this->json('POST', 'api/roles', [
            'name' => 'Guest'
        ]);
        $response->assertStatus(201);
        $response->assertHeader('location');
        $this->assertDatabaseHas('roles', [
            'name' => 'Guest'
        ]);
    }

    public function test_it_validates_input_for_roles()
    {
        Passport::actingAs(User::first());
        $response = $this->json('POST', 'api/roles', [

        ]);
        $response->assertStatus(422);
    }
}
