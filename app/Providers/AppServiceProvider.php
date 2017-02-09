<?php

namespace App\Providers;

use App\Services\Users\UsersService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Users\UsersServiceContract;

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
    }
}
