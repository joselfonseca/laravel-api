## Laravel API Starter Kit

[![Total Downloads](https://poser.pugx.org/joselfonseca/laravel-api/downloads.svg)](https://packagist.org/packages/joselfonseca/laravel-api) 
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

![](https://dev-to-uploads.s3.amazonaws.com/i/4om2rgvulc688tlcj31c.jpg)

Laravel API starter Kit will provide you with the tools for making API's that everyone will love, API Authentication is already provided with passport. 

Here is a list of the packages installed:

- [Laravel Passport](https://laravel.com/docs/8.x/passport)
- [Laravel Socialite](https://laravel.com/docs/8.x/socialite)
- [Laravel Fractal](https://github.com/spatie/laravel-fractal)
- [Laravel Permission](https://github.com/spatie/laravel-permission)
- [Intervention Image](http://image.intervention.io/)

## Installation

To install the project you can use composer

```bash
composer create-project joselfonseca/laravel-api new-api
```

Modify the .env file to suit your needs

```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

When you have the .env with your database connection set up you can run your migrations

```bash
php artisan migrate
```
Then run `php artisan passport:install`

Run `php artisan db:seed` and you should have a new user with the roles and permissions set up

## Tests

Navigate to the project root and run `vendor/bin/phpunit` after installing all the composer dependencies and after the .env file was created.

## API documentation
The project uses API blueprint as API spec and [Aglio](https://github.com/danielgtaylor/aglio) to render the API docs, please install aglio and [merge-apib](https://github.com/ValeriaVG/merge-apib) in your machine and then you can run the following command to compile and render the API docs 
```bash
composer api-docs
```

## License

The Laravel API Starter kit is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
