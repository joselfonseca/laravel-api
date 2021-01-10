<?php

namespace App\Providers;

use App\OAuthGrants\SocialGrant;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Bridge\UserRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->extendAuthorizationServer();
        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addHours(24));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(60));
    }

    protected function makeSocialRequestGrant()
    {
        $grant = new SocialGrant(
            $this->app->make(UserRepository::class),
            $this->app->make(RefreshTokenRepository::class)
        );
        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }

    protected function extendAuthorizationServer()
    {
        $this->app->extend(AuthorizationServer::class, function ($server) {
            return tap($server, function ($server) {
                $server->enableGrantType(
                    $this->makeSocialRequestGrant(),
                    Passport::tokensExpireIn()
                );
            });
        });
    }
}
