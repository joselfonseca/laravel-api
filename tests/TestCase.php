<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function installApp($mail = 'jose@example.com', $name = 'Jose Fonseca')
    {
        $service = app(\App\Services\Installation\AppInstallationService::class);
        $service->installApp([
            'name' => $name,
            'email' => $mail,
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ]);
    }

}
