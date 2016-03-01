## Laravel API Starter Kit

[![Build Status](https://travis-ci.org/joselfonseca/laravel-api.svg)](https://travis-ci.org/joselfonseca/laravel-api)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel API starter Kit will provide you with the tools for making API's that everyone will love, it brings the power of Dingo/Api already set up to make it easier to handle routing, versioning, responses and much more, Authentication is already provided with an oAuth2 server with the `client_credentials`, `password` and `refresh token` grants so you don't have to worry about installing and setting it up yourself. We brought the power of the [Tactician Command bus](http://tactician.thephpleague.com/) to laravel in a simple package [https://github.com/joselfonseca/laravel-tactician](https://github.com/joselfonseca/laravel-tactician) created and maintained by [Jose Fonseca](https://github.com/joselfonseca).

If you like to work with repositories we also brought the popular Laravel 5 repositories package as well as the Eloquence package to make your models more powerful.

Because we now hiding the auto-incremental ID's from the database is important, we have added Laravel UUID which will help you create UUID's for your database records. 

Here is a list of the packages installed:

- [Dingo API](https://github.com/dingo/api)
- [OAuth 2 Server](https://github.com/lucadegasperi/oauth2-server-laravel)
- [Laravel Tactician Command Bus](https://github.com/joselfonseca/laravel-tactician)
- [Laravel 5 Repositories](https://github.com/andersao/l5-repository)
- [Entrust Roles and Permissions](https://github.com/Zizaco/entrust)
- [Eloquence](https://github.com/jarektkaczyk/eloquence)
- [Eloquent Sluggable](https://github.com/cviebrock/eloquent-sluggable)
- [Laravel UUID](https://github.com/webpatser/laravel-uuid)

## Installation

To install the project you can use composer

```bash
composer create-project joselfonseca/laravel-api new-api
```

Once all the dependencies have been installed you can modify the .env file to suit your needs

```
APP_ENV=local
APP_DEBUG=true
APP_KEY=5vU3pFiU7oEm9uIiLuYmTRW87qxVR10b

DB_HOST=localhost
DB_DATABASE=laravel_api
DB_USERNAME=homestead
DB_PASSWORD=secret

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

MAIL_DRIVER=smtp
MAIL_HOST=mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null

API_PREFIX=api
API_VERSION=v1
API_NAME="Laravel API"
API_DEBUG=true
```

When you have the .env with your database connection set up you can run your migrations

```bash
php artisan migrate
```

You should be done with the basic configuration.

## Homestead VM

The Laravel API ships with a Homestead VM that you can use as your development environment.

- Create a `Homestead.yaml` file based on `Homestead.yaml.example` and modify it to suit your needs.
- Run `vagrant up` from the project root to initialize the VM.
- Don't forget to update your hosts file to point the domain to the Homestead machine.

For more information about Homestead visit the official documentation [https://laravel.com/docs/5.1/homestead#per-project-installation](https://laravel.com/docs/5.1/homestead#per-project-installation)

## oAuth 2 Authentication server

The started kit has already implemented the oAuth2 server bridge package for League/oAuth2 which means you get API authentication out of the box

### Authenticating with the oAuth2 server

The available grants by default are:

- Client Credentials Grant
- Password Grant
- Refresh Token Grant

First you will need to create a client in the `oauth_clients` table, you can refer to the test [here](https://github.com/joselfonseca/laravel-api/blob/master/tests/Auth/OAuthServerTest.php#L71) for an example. Once you have a client you can send the request to authenticate using this grant to the endpoint `/api/oauth/authorize`

```
POST /api/oauth/authorize HTTP/1.1
Host: laravel-api.dev
Content-Type: application/x.v1+json
Cache-Control: no-cache
Postman-Token: a5a87ad4-de46-797a-9129-dfd7b78352ac

{
    "grant_type": "client_credentials",
    "client_id" : "test_client",
    "client_secret": "uytyh5y6rte537uejee7"
}
```

If the credentials are correct it should return your access token

```json
{
  "access_token": "4URpSypWno4mNhxbRhjPwwl9i3Q1Ve2ZG83KylmJ",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

if the request is missing data it should return the appropriate 400 response

```json
{
  "errors": {
    "status": "400",
    "code": "InvalidRequest",
    "title": "Invalid Request",
    "detail": "The body does not have the necessary data to process the transaction",
    "source": {
      "parameter": "client_secret"
    }
  }
}
```

if the credentials are incorrect it should return the appropriate 401 response

```json
{
  "errors": {
    "status": "401",
    "code": "InvalidClient",
    "title": "Invalid Client",
    "detail": "The client requesting the information is not registered in the API"
  }
}
```

For more information please visit [The oAuth2 repository](https://github.com/lucadegasperi/oauth2-server-laravel/tree/master/docs) 

## Routing

We use Dingo/Api for routing, this means you have all the methods available [here](https://github.com/dingo/api/wiki/Creating-API-Endpoints)

### Example routes

```php 
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
        $api->group(['prefix' => 'oauth'], function ($api) {
            $api->post('authorize', 'App\Http\Controllers\Auth\AuthController@authorizeClient');
        });
        // Protected routes
        $api->group(['middleware' => 'api.auth', 'namespace' => 'App\Http\Controllers'], function($api){
            // profile routes
            $api->get('me', 'Users\ProfileController@me');
            // users routes
            $api->resource('users', 'Users\UsersController');
        });
    }
);
```

## Uuid

The started kit comes with a UUID observer to add to your models, this way the uuid will be generated when the model is being created.

This is an example for the User model.

```php
namespace App\Providers;

use App\Entities\Users\User;
use App\Observers\UuidObserver;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);
        $this->registerObservers();
    }

    /**
     * @return $this
     */
    public function registerObservers()
    {
        User::observe(app(UuidObserver::class));
        return $this;
    }
}
```

## Tests

Navigate to the project root and run `vendor/bin/phpunit` after installing all the composer dependencies and after the .env file was created.

## License

The Laravel API Starter kit is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
