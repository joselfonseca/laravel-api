<?php

namespace App\Providers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        app('Dingo\Api\Exception\Handler')->register(function (ModelNotFoundException $exception) {
            return $this->response->errorNotFound('404 Not Found');
        });
    }
}
