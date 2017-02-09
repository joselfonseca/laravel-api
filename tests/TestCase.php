<?php

namespace Tests;

use Exception;
use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
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

    public function disableErrorHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report(Exception $e) {}
            public function render($request, Exception $e) {
                throw $e;
            }
        });
    }
}
