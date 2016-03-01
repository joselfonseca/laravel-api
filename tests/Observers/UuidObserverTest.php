<?php

namespace App\Tests\Observers;

use App\Tests\TestCase;
use App\Entities\Users\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UuidObserverTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * @test
     */
    public function itGeneratesUuidForUser()
    {
        $user = factory(User::class)->create();
        $this->assertFalse(empty($user->uuid));
        $this->seeInDatabase('users', [
            'uuid' => $user->uuid->string
        ]);
    }

}