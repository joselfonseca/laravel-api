<?php

namespace Tests\Feature\Auth;

use App\Events\ForgotPasswordRequested;
use App\Events\PasswordRecovered;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_recovery_email()
    {
        Notification::fake();
        Event::fake([ForgotPasswordRequested::class]);
        $user = User::factory()->create();
        $this->postJson('/api/passwords/reset', [
            'email' => $user->email,
        ])->assertCreated();
        Notification::assertSentTo($user, ResetPassword::class);
        Event::assertDispatched(ForgotPasswordRequested::class, function ($event) use ($user) {
            return $event->email === $user->email;
        });
    }

    public function test_it_validates_input()
    {
        Notification::fake();
        Event::fake([ForgotPasswordRequested::class]);
        $this->postJson('/api/passwords/reset', [])->assertStatus(422);
        Notification::assertNothingSent();
        Event::assertNotDispatched(ForgotPasswordRequested::class);
    }

    public function test_it_recovers_password_with_token()
    {
        Event::fake([PasswordRecovered::class]);
        $user = User::factory()->create();
        $broker = Password::broker();
        $token = $broker->createToken($user);
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ]);
        $this->putJson('/api/passwords/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Abc123**',
        ])->assertOk();
        Event::assertDispatched(PasswordRecovered::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }

    public function test_it_validates_token_in_password_reset()
    {
        Event::fake([PasswordRecovered::class]);
        $user = User::factory()->create();
        $this->putJson('/api/passwords/reset', [
            'token' => 'some-token',
            'email' => $user->email,
            'password' => 'Abc123**',
        ])->assertStatus(422);
        Event::assertNotDispatched(PasswordRecovered::class);
    }
}
