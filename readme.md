## Laravel API Starter Kit

[![Build Status](https://travis-ci.org/joselfonseca/laravel-api.svg)](https://travis-ci.org/joselfonseca/laravel-api)
[![Total Downloads](https://poser.pugx.org/joselfonseca/laravel-api/downloads.svg)](https://packagist.org/packages/joselfonseca/laravel-api) 
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel API starter Kit will provide you with the tools for making API's that everyone will love, API Authentication is already provided with passport. We brought the power of the [Tactician Command bus](http://tactician.thephpleague.com/) to laravel in a simple package [https://github.com/joselfonseca/laravel-tactician](https://github.com/joselfonseca/laravel-tactician) created and maintained by [Jose Fonseca](https://github.com/joselfonseca). 

Here is a list of the packages installed:

- [Laravel Tactician Command Bus](https://github.com/joselfonseca/laravel-tactician)
- [Laravel API Tools](https://github.com/joselfonseca/laravel-api-tools)
- [Laravel Passport](https://laravel.com/docs/5.4/passport)
- [Laravel Permission](https://github.com/spatie/laravel-permission)
- [Laravel Uuid](https://github.com/webpatser/laravel-uuid)

## Installation

To install the project you can use composer

```bash
composer create-project joselfonseca/laravel-api new-api
```

You may receive an error about the key files after the installation, that is OK. To fix it run the following in the project's root

```
touch storage/oauth-private.key
touch storage/oauth-public.key
```

Then run `composer install` again and the error should be gone.

Modify the .env file to suit your needs

```
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:JqyMTmt5qr1CW6BH+GG+4iKfU4RiNjZTLy33TdTT7+4=

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

Run `yarn install` to install node dependencies

Run `npm run dev` to compile the CSS and JS

You should be done with the basic configuration.

## Tests

Navigate to the project root and run `vendor/bin/phpunit` after installing all the composer dependencies and after the .env file was created.

## License

The Laravel API Starter kit is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
