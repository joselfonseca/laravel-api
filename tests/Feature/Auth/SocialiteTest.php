<?php

namespace Tests\Feature\Auth;

use App\Models\SocialProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SocialiteTest extends TestCase
{
    use RefreshDatabase;

    public function mockSocialite(SocialProvider $provider)
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser
            ->shouldReceive('getId')
            ->andReturn($provider->provider_id)
            ->shouldReceive('getName')
            ->andReturn($provider->user->name)
            ->shouldReceive('getEmail')
            ->andReturn($provider->user->email);
        Socialite::shouldReceive('driver->userFromToken')->andReturn($abstractUser);
    }

    public function mockSocialiteWithoutUser()
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser
            ->shouldReceive('getId')
            ->andReturn('fakeId')
            ->shouldReceive('getName')
            ->andReturn('Jose Fonseca')
            ->shouldReceive('getEmail')
            ->andReturn('jose@example.com');
        Socialite::shouldReceive('driver->userFromToken')->andReturn($abstractUser);
    }

    public function mockSocialiteWithUser(User $user)
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser
            ->shouldReceive('getId')
            ->andReturn('fakeId')
            ->shouldReceive('getName')
            ->andReturn('Jose Fonseca')
            ->shouldReceive('getEmail')
            ->andReturn($user->email);
        Socialite::shouldReceive('driver->userFromToken')->andReturn($abstractUser);
    }

    public function createClient()
    {
        return Client::factory()->create();
    }

    public function test_it_generates_tokens_with_social_grant_for_existing_user()
    {
        $provider = SocialProvider::factory()->create();
        $this->mockSocialite($provider);
        $client = $this->createClient();
        $response = $this->postJson('oauth/token', [
            'grant_type' => 'social_grant',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'token' => Str::random(),
            'provider' => 'github',
        ])->assertOk();
        $decodedResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('access_token', $decodedResponse);
        $this->assertArrayHasKey('refresh_token', $decodedResponse);
    }

    public function test_it_generates_tokens_with_social_grant_for_non_existing_user()
    {
        $this->mockSocialiteWithoutUser();
        $client = $this->createClient();
        $response = $this->postJson('oauth/token', [
            'grant_type' => 'social_grant',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'token' => Str::random(),
            'provider' => 'github',
        ])->assertOk();
        $decodedResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('access_token', $decodedResponse);
        $this->assertArrayHasKey('refresh_token', $decodedResponse);
        $this->assertDatabaseHas('users', [
            'email' => 'jose@example.com',
            'name' => 'Jose Fonseca',
        ]);
        $createdUser = User::first();
        $this->assertDatabaseHas('social_providers', [
            'user_id' => $createdUser->id,
            'provider' => 'github',
            'provider_id' => 'fakeId',
        ]);
    }

    public function test_it_generates_tokens_with_social_grant_for_existing_user_without_social_provider()
    {
        $user = User::factory()->create();
        $this->mockSocialiteWithUser($user);
        $this->assertDatabaseMissing('social_providers', [
            'user_id' => $user->id,
        ]);
        $client = $this->createClient();
        $response = $this->postJson('oauth/token', [
            'grant_type' => 'social_grant',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'token' => Str::random(),
            'provider' => 'github',
        ])->assertOk();
        $decodedResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('access_token', $decodedResponse);
        $this->assertArrayHasKey('refresh_token', $decodedResponse);
        $this->assertDatabaseHas('social_providers', [
            'user_id' => $user->id,
            'provider' => 'github',
            'provider_id' => 'fakeId',
        ]);
    }
}
