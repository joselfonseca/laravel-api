<?php

$api = app('Dingo\Api\Routing\Router');
$api->version(
    'v1',
    function ($api) {
        $api->group(
            ['prefix' => 'oauth'],
            function ($api) {
                $api->post('authorize', 'App\Http\Controllers\Auth\AuthController@authorizeClient');
            }
        );
    }
);
