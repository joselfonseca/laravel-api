<?php

namespace Tests\Feature\Users;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PermissionsEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }

    public function test_it_can_list_permissions()
    {
        Passport::actingAs(User::first());
        $response = $this->json('GET', 'api/permissions');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['name' => 'List users'],
                ['name' => 'Create users'],
            ],
            'meta' => [
                'pagination' => [

                ],
            ],
        ]);
    }

    public function test_it_can_list_paginated_permissions()
    {
        Permission::factory()->count(30)->create();
        Passport::actingAs(User::first());
        $response = $this->json('GET', 'api/permissions?limit=10');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['name' => 'List users'],
                ['name' => 'Create users'],
            ],
            'meta' => [
                'pagination' => [

                ],
            ],
        ]);
        $jsonResponse = json_decode($response->getContent(), true);
        $queryString = explode('?', $jsonResponse['meta']['pagination']['links']['next']);
        parse_str($queryString[1], $result);
        $this->assertArrayHasKey('limit', $result);
        $this->assertEquals('10', $result['limit']);
    }

    public function test_it_prevents_unauthorized_list_permissions()
    {
        Passport::actingAs(User::factory()->create());
        $response = $this->json('GET', 'api/permissions');
        $response->assertStatus(403);
    }
}
