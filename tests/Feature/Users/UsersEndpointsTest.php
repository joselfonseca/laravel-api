<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Entities\User;
use Faker\Factory as Faker;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UsersEndpointsTest extends TestCase
{

    use DatabaseMigrations;

    protected $_faker;

    function setUp()
    {
        parent::setUp();
        $this->installApp();
        $this->_faker = Faker::create();
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
        $this->assertArrayHasKey('included', $jsonResponse);
        $this->assertArrayHasKey('pagination', $jsonResponse['meta']);
        $this->assertEquals(31, $jsonResponse['meta']['pagination']['total']);
        $this->assertEquals(20, $jsonResponse['meta']['pagination']['count']);
        $this->assertCount(20, $jsonResponse['data']);
        $this->assertArrayHasKey('id', $jsonResponse['data'][0]);
        $this->assertArrayHasKey('attributes', $jsonResponse['data'][0]);
        $this->assertArrayHasKey('links', $jsonResponse['data'][0]);
        $this->assertArrayHasKey('relationships', $jsonResponse['data'][0]);
        $this->assertArrayHasKey('name', $jsonResponse['data'][0]['attributes']);
        $this->assertArrayHasKey('email', $jsonResponse['data'][0]['attributes']);
        $this->assertArrayHasKey('roles', $jsonResponse['data'][0]['relationships']);
        $this->assertArrayHasKey('data', $jsonResponse['data'][0]['relationships']['roles']);
        $this->assertArrayHasKey('self', $jsonResponse['data'][0]['relationships']['roles']['links']);
        $this->assertArrayHasKey('related', $jsonResponse['data'][0]['relationships']['roles']['links']);
        $this->assertEquals('roles', $jsonResponse['included'][0]['type']);
    }

    function test_it_list_second_page_of_users()
    {
        factory(\App\Entities\User::class, 30)->create();
        Passport::actingAs(User::first());
        $response = $this->json('GET','api/users?page=2');
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(200);
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $jsonResponse);
        $this->assertArrayHasKey('meta', $jsonResponse);
        $this->assertArrayHasKey('pagination', $jsonResponse['meta']);
        $this->assertEquals(31, $jsonResponse['meta']['pagination']['total']);
        $this->assertEquals(11, $jsonResponse['meta']['pagination']['count']);
        $this->assertEquals(2, $jsonResponse['meta']['pagination']['current_page']);
        $this->assertCount(11, $jsonResponse['data']);
    }

    function test_it_lists_users_filtered_by_email()
    {
        $faker = Faker::create();
        $email1 = $faker->email;
        $email2 = $faker->email;
        factory(\App\Entities\User::class)->create([
            'email' => $email1,
        ]);
        factory(\App\Entities\User::class)->create([
            'email' => $email2,
        ]);
        Passport::actingAs(User::first());
        $response = $this->json('GET','api/users?email=' . $email1);
        $response->assertStatus(200);
        $response = json_decode($response->getContent(), true);
        $this->assertEquals(1, $response['meta']['pagination']['total']);
        $this->assertCount(1, $response['data']);
        $this->assertEquals($email1, $response['data'][0]['attributes']['email']);
    }

    function test_it_list_users_filtered_by_email_and_shows_correct_meta_information()
    {
        $faker = Faker::create();
        $domain = 'myowndomain.com.ec';
        for ($i = 0; $i < 50; $i++) {
            $username = $faker->unique()->userName;
            factory(\App\Entities\User::class)->create([
                'email' => $username . '@' . $domain,
            ]);
        }
        factory(\App\Entities\User::class, 50)->create();
        Passport::actingAs(User::first());
        $response = $this->json('GET', 'api/users?email=' . $domain);
        $response = json_decode($response->getContent(), true);
        $this->assertEquals(50, $response['meta']['pagination']['total']);
        $this->assertEquals(20, $response['meta']['pagination']['count']);
        $this->assertEquals(1, $response['meta']['pagination']['current_page']);
        $this->assertEquals('http://localhost/api/users?email=' . $domain . '&page=2', $response['links']['next']);
        $this->assertCount(20, $response['data']);
    }

    function test_it_list_users_changing_limit_value_in_query()
    {
        $limit = 13;
        factory(\App\Entities\User::class, 50)->create();
        Passport::actingAs(User::first());
        $response = $this->json('GET','api/users?limit=' . 13);
        $response = json_decode($response->getContent(), true);
        $this->assertEquals(51, $response['meta']['pagination']['total']);
        $this->assertEquals(13, $response['meta']['pagination']['count']);
        $this->assertCount(13, $response['data']);
        $this->assertEquals('http://localhost/api/users?limit=' . $limit . '&page=2', $response['links']['next']);
    }

    function test_it_lists_users_ordered_by_name_asc()
    {
        $faker = Faker::create();

        $names = ['Jose Fonseca'];

        $number = 15;

        for ($i = 0; $i < $number; $i++) {
            $name = $faker->name;
            array_push($names, $name);
            factory(\App\Entities\User::class)->create([
                'name' => $name,
            ]);
        }

        Passport::actingAs(User::first());

        $response = $this->json('GET','api/users?orderBy=name');

        $response = json_decode($response->getContent(), true);
        sort($names);
        for ($i=0; $i < $number; $i++) {
            $this->assertEquals($names[$i], $response['data'][$i]['attributes']['name']);
        }
    }

    function test_it_lists_users_ordered_by_email_desc()
    {
        $faker = Faker::create();

        $emails = ['jose@example.com'];

        $number = 15;

        for ($i = 0; $i < $number; $i++) {
            $email = $faker->email;
            array_push($emails, $email);
            factory(\App\Entities\User::class)->create([
                'email' => $email,
            ]);
        }

        Passport::actingAs(User::first());
        $response = $this->json('GET', 'api/users?orderBy=email:desc');
        $response = json_decode($response->getContent(), true);
        rsort($emails);
        for ($i=0; $i < $number; $i++) {
            $this->assertEquals($emails[$i], $response['data'][$i]['attributes']['email']);
        }
    }

    function test_it_validates_permission_for_listing_users()
    {
        factory(\App\Entities\User::class, 30)->create();
        $user = factory(\App\Entities\User::class)->create([
            'email' => 'me@example.com'
        ]);
        Passport::actingAs($user);
        $response = $this->json('GET', '/api/users');
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
        $this->assertEquals('me@example.com', $jsonResponse['data']['attributes']['email']);
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
        ], [
            'Accept' => 'application/vnd.api.v1+json'
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

    function test_it_can_update_a_user_with_full_entity()
    {
        Passport::actingAs(User::first());
        $user = factory(\App\Entities\User::class)->create();
        $response = $this->json('PUT', 'api/users/'.$user->uuid, [
            'name' => 'Jose Fonseca',
            'email' => 'new@example.com'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'name' => 'Jose Fonseca',
            'id' => $user->id,
            'email' => 'new@example.com'
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

        ], [
            'Accept' => 'application/vnd.api.v1+json'
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

    function test_it_can_delete_multiple_user()
    {
        $user1 = factory(\App\Entities\User::class)->create();
        $user2 = factory(\App\Entities\User::class)->create();

        Passport::actingAs(User::first());

        $response = $this->json('DELETE', 'api/users/' . $user1->uuid . ',' . $user2->uuid);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', [
            'id' => $user1->id,
            'deleted_at' => null
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user2->id,
            'deleted_at' => null
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user1->id
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user2->id
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

    function test_it_can_update_user_password()
    {

        Passport::actingAs(User::first());

        $password1 = $this->_faker->md5;
        $password2 = $this->_faker->md5;

        $user = factory(\App\Entities\User::class)->create([
            'password' => bcrypt($password1),
        ]);

        $model = new \App\Entities\User;

        $userWithOldPassword = $model->find($user->id);

        $this->assertTrue(Hash::check($password1, $userWithOldPassword->password));

        $response = $this->json('PATCH', 'api/users/'.$user->uuid, [
            'password' => $password2,
            'password_confirmation' => $password2
        ]);

        $response->assertStatus(200);

        $userWithNewPassword = $model->find($user->id);

        $this->assertFalse(Hash::check($password1, $userWithNewPassword->password));

        $this->assertTrue(Hash::check($password2, $userWithNewPassword->password));
    }

    function test_it_can_not_update_password_because_password_confirmation_is_empty()
    {

        Passport::actingAs(User::first());

        $password1 = $this->_faker->md5;
        $password2 = $this->_faker->md5;

        $user = factory(\App\Entities\User::class)->create([
            'password' => Hash::make($password1),
        ]);

        $response = $this->json('PATCH', 'api/users/'.$user->uuid, [
            'password' => $password2
        ]);

        $response->assertStatus(422);
    }

    function test_it_can_not_update_user_password_because_password_is_empty()
    {
        Passport::actingAs(User::first());
        $password1 = $this->_faker->md5;

        $user = factory(\App\Entities\User::class)->create([
            'password' => Hash::make($password1),
        ]);

        $model = new \App\Entities\User;

        $userWithOldPassword = $model->find($user->id);

        $this->assertTrue(Hash::check($password1, $userWithOldPassword->password));

        $response = $this->json('PATCH','api/users/'.$user->uuid, [
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertStatus(422);

        $userWithNewPassword = $model->find($user->id);

        $this->assertTrue(Hash::check($password1, $userWithNewPassword->password));
    }

    function test_it_can_update_email_of_user()
    {
        Passport::actingAs(User::first());
        $email1 = $this->_faker->unique()->email;
        $email2 = $this->_faker->unique()->email;

        $user = factory(\App\Entities\User::class)->create([
            'email' => $email1,
        ]);

        $response = $this->json('PATCH', 'api/users/'.$user->uuid, [
            'email' => $email2,
        ]);

        $response->assertStatus(200);

        $userWithNewEmail = \App\Entities\User::find($user->id);

        $this->assertEquals($email2, $userWithNewEmail->email);
    }

    function test_it_can_not_update_email_of_user_because_email_is_already_taken_by_other_user()
    {
        Passport::actingAs(User::first());
        $email1 = $this->_faker->unique()->email;
        $email2 = $this->_faker->unique()->email;

        $user1 = factory(\App\Entities\User::class)->create([
            'email' => $email1,
        ]);

        $user2 = factory(\App\Entities\User::class)->create([
            'email' => $email2,
        ]);

        $response = $this->json('PATCH', 'api/users/'.$user1->uuid, [
            'email' => $email2,
        ]);

        $response->assertStatus(422);
    }

    function test_it_can_update_user_writing_same_mail()
    {
        Passport::actingAs(User::first());

        $email1 = $this->_faker->unique()->email;
        $name1 = $this->_faker->name;
        $name2 = $this->_faker->name;

        $user1 = factory(\App\Entities\User::class)->create([
            'name' => $name1,
            'email' => $email1,
        ]);

        $response = $this->json('PATCH', 'api/users/'.$user1->uuid, [
            'name' => $name2,
            'email' => $email1,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'name' => $name2,
            'email' => $email1,
            'id' => $user1->id
        ]);
    }

}