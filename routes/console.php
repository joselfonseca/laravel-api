<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('dev:generate-personal-token {userId}', function ($userId) {
    $user = \App\Entities\User::find($userId);
    $this->info('Token for user '.$user->name);
    $token = $user->createToken('Personal Access Token')->accessToken;
    $this->info($token);
})->describe('Generates a personal access token for a user');
