## Laravel API Starter Kit

[![Build Status](https://travis-ci.org/joselfonseca/laravel-api.svg)](https://travis-ci.org/joselfonseca/laravel-api)
[![StyleCI](https://styleci.io/repos/52485545/shield?branch=master)](https://styleci.io/repos/52485545)
[![Total Downloads](https://poser.pugx.org/joselfonseca/laravel-api/downloads.svg)](https://packagist.org/packages/joselfonseca/laravel-api) 
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel API starter Kit will provide you with the tools for making API's that everyone will love, API Authentication is already provided with passport. 

Here is a list of the packages installed:

- [Dingo API](https://github.com/dingo/api)
- [Laravel Passport](https://laravel.com/docs/5.7/passport)
- [Laravel Permission](https://github.com/spatie/laravel-permission)
- [Intervention Image](http://image.intervention.io/)

## Installation

To install the project you can use composer

```bash
composer create-project joselfonseca/laravel-api new-api
```

Then run `composer install` again and the error should be gone.

Modify the .env file to suit your needs

```
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:JqyMTmt5qr1CW6BH+GG+4iKfU4RiNjZTLy33TdTT7+4=

API_STANDARDS_TREE=vnd
API_SUBTYPE=api
API_PREFIX=api
API_VERSION=v1
API_DEBUG=true

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
```

When you have the .env with your database connection set up you can run your migrations

```bash
php artisan migrate
```
Then run `php artisan passport:install`

Run `php artisan app:install` and fill out the information of the admin user.

You should be done with the basic installation and configuration.

## Tests

Navigate to the project root and run `vendor/bin/phpunit` after installing all the composer dependencies and after the .env file was created.

## License

The Laravel API Starter kit is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
