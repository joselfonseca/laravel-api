<?php

namespace Tests\Feature\Users;

use Tests\TestCase;
use App\Entities\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PermissionsEndpointsTest extends TestCase
{

    use DatabaseMigrations;


    function setUp()
    {
        parent::setUp();
        $this->installApp();
    }

    function test_it_can_list_permissions()
    {
        Passport::actingAs(User::first());
        $response = $this->json('GET', 'api/permissions');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['name' => 'List users'],
                ['name' => 'Create users']
            ],
            'meta' => [
                'pagination' => [

                ]
            ]
        ]);
    }

    function test_it_prevents_unauthorized_list_permissions()
    {
        Passport::actingAs(factory(User::class)->create());
        $response = $this->json('GET', 'api/permissions');
        $response->assertStatus(403);
    }

}
