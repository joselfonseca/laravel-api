<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Entities\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UsersEndpointsTest extends TestCase
{

    use DatabaseMigrations;

    function setUp()
    {
        parent::setUp();
        $this->installApp();
    }

    function test_it_list_users()
    {
        factory(\App\Entities\User::class, 30)->create();
        Passport::actingAs(User::first());
        $response = $this->json('GET', 'api/users');
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(200);
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $jsonResponse);
        $this->assertArrayHasKey('meta', $jsonResponse);
        $this->assertArrayHasKey('pagination', $jsonResponse['meta']);
        $this->assertEquals(31, $jsonResponse['meta']['pagination']['total']);
        $this->assertEquals(20, $jsonResponse['meta']['pagination']['count']);
        $this->assertCount(20, $jsonResponse['data']);
        $this->assertArrayHasKey('id', $jsonResponse['data'][0]);
        $this->assertArrayHasKey('name', $jsonResponse['data'][0]);
        $this->assertArrayHasKey('email', $jsonResponse['data'][0]);
        $this->assertArrayHasKey('data', $jsonResponse['data'][0]['roles']);
    }

}