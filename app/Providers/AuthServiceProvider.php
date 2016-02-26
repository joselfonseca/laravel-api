<?php

namespace App\Providers;

use Dingo\Api\Auth\Provider\OAuth2;
use App\Contracts\UserResolverInterface;
use App\Contracts\ClientResolverInterface;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Class AuthServiceProvider
 *
 * @package App\Providers
 */
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
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        parent::registerPolicies($gate);
        $this->registerOAuthProvider();
    }

    /**
     * Register the oAuth 2 server provider
     */
    public function registerOAuthProvider()
    {
        app('Dingo\Api\Auth\Auth')->extend(
            'oauth', function ($app) {
                $provider = new OAuth2($app['oauth2-server.authorizer']->getChecker());

                $provider->setUserResolver(
                    function ($id) {
                        $resolver = app(UserResolverInterface::class);
                        return $resolver->resolveById($id);
                    }
                );

                $provider->setClientResolver(
                    function ($id) {
                        $resolver = app(ClientResolverInterface::class);
                        return $resolver->resolveById($id);
                    }
                );

                return $provider;
            }
        );
    }
}
