<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }

    public function test_it_register_user_with_role()
    {
        Event::fake([Registered::class]);
        $response = $this->json('POST', 'api/register/', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue($user->hasRole('User'));
        Event::assertDispatched(Registered::class, function ($event) use ($user) {
            return $user->id === $event->user->id;
        });
    }

    public function test_it_validates_input_for_registration()
    {
        Event::fake([Registered::class]);
        $response = $this->json('POST', 'api/register', [
            'name' => 'Some User',
            'email' => 'some@email.com',
            'password' => '123456789qq',
        ]);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', [
            'name' => 'Some User',
            'email' => 'some@email.com',
        ]);
        Event::assertNotDispatched(Registered::class);
    }

    public function test_it_returns_422_on_validation_error()
    {
        Event::fake([Registered::class]);
        $response = $this->json('POST', 'api/register', [
            'name' => 'Some User',
        ]);
        $response->assertStatus(422);
        $this->assertEquals('{"message":"The given data was invalid.","status_code":422,"errors":{"email":["The email field is required."],"password":["The password field is required."]}}', $response->getContent());
        $this->assertDatabaseMissing('users', [
            'name' => 'Some User',
            'email' => 'some@email.com',
        ]);
        Event::assertNotDispatched(Registered::class);
    }
}
