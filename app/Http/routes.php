<?php

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['prefix' => 'auth'], function($api){
        $api->post('login', 'App\Http\Controllers\Auth\AuthController@login');
        $api->post('refresh-token', 'App\Http\Controllers\Auth\AuthController@refreshToken');
    });
});
