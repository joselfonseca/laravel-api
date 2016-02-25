## Laravel API Starter Kit

[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel API starter Kit will give you all the boilerplate that you need for:

- [Dingo API](https://github.com/dingo/api)
- [OAuth 2 Server](https://github.com/lucadegasperi/oauth2-server-laravel)
- [Laravel Tactician Command Bus](https://github.com/joselfonseca/laravel-tactician)
- [Laravel 5 Repositories](https://github.com/andersao/l5-repository)
- [Entrust Roles and Permissions](https://github.com/Zizaco/entrust)
- [Eloquence](https://github.com/jarektkaczyk/eloquence)
- [Eloquent Sluggable](https://github.com/cviebrock/eloquent-sluggable)
- [Laravel UUID](https://github.com/webpatser/laravel-uuid)

### Instalation

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

### oAuth 2 Authentication server

The started kit has already implemented the oAuth2 server bridge package for League/oAuth2 which means you get API authentication out of the box

## Authenticating with the oAuth2 server

The available grants by default are:

- Client Credentials Grant
- Password Grant
- Refresh Token Grant

First you will need to create a client in the `oauth_clients` table, you can refer tho the test [here](https://github.com/joselfonseca/laravel-api/blob/master/tests/Auth/OAuthServerTest.php#L71) for an example. Once you have a client you can send the request to authenticate using this grant to the endpoint `/api/oauth/authorize`

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

### Dingo/Api

This started kit has already set up [dingo/api](https://github.com/dingo/api) to manage routes, requests, responses, protecting endpoint, versioning and much more, for more details please visit [https://github.com/dingo/api](https://github.com/dingo/api) 

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
