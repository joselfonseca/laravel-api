<?php

namespace App\Providers;

use App\Services\Users\UsersService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Users\UsersServiceContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UsersServiceContract::class, UsersService::class);
        app('Dingo\Api\Exception\Handler')->register(function (ModelNotFoundException $exception) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        });
    }
}
