<?php

namespace App\Providers;

use App\Services\RolesService;
use App\Services\UsersService;
use App\Services\AssetsService;
use App\Services\PermissionsService;
use App\Contracts\UsersServiceContract;
use App\Contracts\RolesServiceContract;
use Illuminate\Support\ServiceProvider;
use App\Contracts\AssetsServiceContract;
use App\Contracts\PermissionsServiceContract;

/**
 * Class AppServiceProvider
 *
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $bindings = [
        UsersServiceContract::class => UsersService::class,
        RolesServiceContract::class => RolesService::class,
        AssetsServiceContract::class => AssetsService::class,
        PermissionsServiceContract::class => PermissionsService::class
    ];

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
        collect($this->bindings)->each(function ($concrete, $abstract) {
            $this->app->bind($abstract, $concrete);
        });
    }
}
