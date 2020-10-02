<?php

namespace App\Providers;

use App\Exceptions\BodyTooLargeException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;

class ErrorHandlerServiceProvider extends ServiceProvider
{
    use Helpers;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application error handlers.
     *
     * @return void
     */
    public function register()
    {
        // register the error handler for the authentication exception.
        app('Dingo\Api\Exception\Handler')->register(function (AuthenticationException $exception) {
            return $this->response->errorUnauthorized('Unauthenticated.');
        });
        app('Dingo\Api\Exception\Handler')->register(function (BodyTooLargeException $exception) {
            return $this->response->error('The body is too large', 413);
        });
        app('Dingo\Api\Exception\Handler')->register(function (ValidationException $exception) {
            throw new ResourceException('Validation Error', $exception->errors());
        });
    }
}
