<?php

namespace Tests\Feature;

use App\Entities\User;
use App\Services\Users\UsersService;
use Laravel\Passport\Passport;
use Tests\TestCase;
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
        $response = $this->json('GET', '/api/users');
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
    }

    function test_it_validates_permission_for_listing_users()
    {
        factory(\App\Entities\User::class, 30)->create();
        $user = factory(\App\Entities\User::class)->create([
            'email' => 'me@example.com'
        ]);
        Passport::actingAs($user);
        $response = $this->json('GET', 'api/users');
        $response->assertStatus(403);
    }

    function test_it_can_show_user()
    {
        $user = factory(\App\Entities\User::class)->create([
            'email' => 'me@example.com'
        ]);
        Passport::actingAs(User::first());
        $response = $this->json('GET', '/api/users/'.$user->uuid);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(200);
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $jsonResponse);
        $this->assertEquals('me@example.com', $jsonResponse['data']['email']);
    }

    function test_it_can_create_user()
    {
        Passport::actingAs(User::first());
        $response = $this->json('POST', 'api/users', [
            'name' => 'Some User',
            'email' => 'some@email.com',
            'password' => '123456789qq',
            'password_confirmation' => '123456789qq'
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'name' => 'Some User',
            'email' => 'some@email.com'
        ]);
    }

    function test_it_validates_input_for_creation()
    {
        Passport::actingAs(User::first());
        $response = $this->json('POST', 'api/users', [
            'name' => 'Some User',
            'email' => 'some@email.com',
            'password' => '123456789qq',
        ]);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', [
            'name' => 'Some User',
            'email' => 'some@email.com'
        ]);
    }

    function test_it_can_update_a_user()
    {
        Passport::actingAs(User::first());
        $user = factory(\App\Entities\User::class)->create();
        $response = $this->json('PATCH', 'api/users/'.$user->uuid, [
            'name' => 'Jose Fonseca'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'name' => 'Jose Fonseca',
            'id' => $user->id
        ]);
    }

    function test_it_validates_input_to_update_a_user()
    {
        Passport::actingAs(User::first());
        $user = factory(\App\Entities\User::class)->create();
        $response = $this->json('PATCH', 'api/users/'.$user->uuid, [
            'name' => ''
        ]);
        $response->assertStatus(422);
    }

    function test_it_can_delete_a_user()
    {
        Passport::actingAs(User::first());
        $user = factory(\App\Entities\User::class)->create();
        $response = $this->json('DELETE', 'api/users/'.$user->uuid, [

        ]);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'deleted_at' => null
        ]);
        // just to make sure there is a user record for the soft delete
        $this->assertDatabaseHas('users', [
            'id' => $user->id
        ]);
    }

    function test_it_protects_the_user_from_being_deleted_by_user_with_no_permission()
    {
        $user = factory(\App\Entities\User::class)->create([
            'email' => 'me@example.com'
        ]);
        $user2 = factory(\App\Entities\User::class)->create([
            'email' => 'me2@example.com'
        ]);
        Passport::actingAs($user);
        $response = $this->json('DELETE', 'api/users/'.$user2->uuid, [

        ]);
        $response->assertStatus(403);
    }

}