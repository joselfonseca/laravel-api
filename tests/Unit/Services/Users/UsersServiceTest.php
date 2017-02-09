<?php

namespace Tests\Unit\Services\Installation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UsersServiceTest extends TestCase
{

    use DatabaseMigrations;

    protected $service;

    function makeService()
    {
        $this->service = app(\App\Services\Users\UsersService::class);
    }

    function test_it_can_create_a_user()
    {
        $this->makeService();
        $data = [
            'name' => 'Jose Fonseca',
            'email' => 'some@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ];
        $model = $this->service->create($data);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Model::class, $model);
        $this->assertInstanceOf(\App\Entities\User::class, $model);
        $this->assertDatabaseHas('users', [
            'name' => 'Jose Fonseca',
            'email' => 'some@example.com',
        ]);
    }

    /**
     * @expectedException \Joselfonseca\LaravelApiTools\Exceptions\ValidationException
     */
    function test_it_validates_user_input_for_creation()
    {
        $this->makeService();
        $data = [
            'name' => 'Jose Fonseca',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ];
        $this->service->create($data);
    }

    /**
     * @expectedException \Joselfonseca\LaravelApiTools\Exceptions\ValidationException
     */
    function test_it_validates_existing_email_for_creation()
    {
        $user = factory(\App\Entities\User::class)->create();
        $this->makeService();
        $data = [
            'name' => 'Jose Fonseca',
            'email' => $user->email,
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ];
        $this->service->create($data);
    }

    /**
     * @expectedException \Joselfonseca\LaravelApiTools\Exceptions\ValidationException
     */
    function test_it_validates_password_confirmation_for_creation()
    {
        $this->makeService();
        $data = [
            'name' => 'Jose Fonseca',
            'email' => 'jose@example.com',
            'password' => '12345'
        ];
        $this->service->create($data);
    }

    /**
     * @expectedException \Joselfonseca\LaravelApiTools\Exceptions\ValidationException
     */
    function test_it_validates_password_length_for_creation()
    {
        $this->makeService();
        $data = [
            'name' => 'Jose Fonseca',
            'email' => 'jose@example.com',
            'password' => '12345',
            'password_confirmation' => '12345'
        ];
        $this->service->create($data);
    }

    function test_it_can_get_paginator_for_users()
    {
        factory(\App\Entities\User::class, 50)->create();
        $this->makeService();
        $paginator = $this->service->get();
        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $paginator);
        $this->assertCount(20, $paginator);
        $this->assertInstanceOf(\App\Entities\User::class, $paginator->first());
    }

    function test_it_gets_all_the_users()
    {
        factory(\App\Entities\User::class, 50)->create();
        $this->makeService();
        $collection = $this->service->get(null);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $collection);
        $this->assertCount(50, $collection);
        $this->assertInstanceOf(\App\Entities\User::class, $collection->first());
    }

    function test_it_gets_a_single_model_by_uuid_and_id()
    {
        $user = factory(\App\Entities\User::class)->create();
        $this->makeService();
        $this->assertInstanceOf(\App\Entities\User::class, $this->service->find($user->uuid));
        $this->assertInstanceOf(\App\Entities\User::class, $this->service->find($user->id));
    }

    function test_it_updates_a_user_name()
    {
        $user = factory(\App\Entities\User::class)->create();
        $this->makeService();
        $this->assertNotEquals('jose fonseca', $user->name);
        $newUser = $this->service->update($user->uuid, [
            'name' => 'jose fonseca'
        ]);
        $this->assertEquals('jose fonseca', $newUser->name);
        $this->assertDatabaseHas('users', [
            'name' => 'jose fonseca'
        ]);
    }

    function test_it_updates_a_user_email()
    {
        $user = factory(\App\Entities\User::class)->create();
        $this->makeService();
        $this->assertNotEquals('jose@example.com', $user->email);
        $newUser = $this->service->update($user->uuid, [
            'email' => 'jose@example.com'
        ]);
        $this->assertEquals('jose@example.com', $newUser->email);
    }

    /**
     * @expectedException \Joselfonseca\LaravelApiTools\Exceptions\ValidationException
     */
    function test_it_validates_email_in_use_on_update()
    {
        $user = factory(\App\Entities\User::class)->create();
        $user2 = factory(\App\Entities\User::class)->create();
        $this->makeService();
        $this->assertNotEquals($user->email, $user2->email);
        $this->service->update($user->uuid, [
            'email' => $user2->email
        ]);
    }

    function test_it_deletes_user()
    {
        $user = factory(\App\Entities\User::class)->create();
        $this->makeService();
        $data = $user->toArray();
        $this->service->delete($user->uuid);
        $this->assertDatabaseMissing('users', [
            'email' => $data['email'],
            'deleted_at' => null
        ]);
    }

}
