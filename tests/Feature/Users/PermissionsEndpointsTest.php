<?php

namespace Tests\Feature\Users;

use Tests\TestCase;
use App\Entities\User;
use App\Entities\Permission;
use Laravel\Passport\Passport;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PermissionsEndpointsTest extends TestCase
{

    use DatabaseMigrations;


    function setUp()
    {
        parent::setUp();
        $this->installApp();
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
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

    function test_it_can_list_paginated_permissions()
    {
        factory(Permission::class, 30)->create();
        Passport::actingAs(User::first());
        $response = $this->json('GET', 'api/permissions?limit=10');
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
        $jsonResponse = json_decode($response->getContent(), true);
        $queryString = explode('?', $jsonResponse['meta']['pagination']['links']['next']);
        parse_str($queryString[1], $result);
        $this->assertArrayHasKey('limit', $result);
        $this->assertEquals('10', $result['limit']);
    }

    function test_it_prevents_unauthorized_list_permissions()
    {
        Passport::actingAs(factory(User::class)->create());
        $response = $this->json('GET', 'api/permissions');
        $response->assertStatus(403);
    }

}
