<?php

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->get('ping', function(){
        return [
            'status' => 'ok',
            'timestamp' => \Carbon\Carbon::now()
        ];
    });

    $api->group(['middleware' => ['api.auth', 'throttle:60,1'], 'providers' => ['passport']], function ($api) {
        $api->get('users', 'App\Http\Controllers\Users\UsersController@index');
        $api->post('users', 'App\Http\Controllers\Users\UsersController@store');
        $api->get('users/{uuid}', 'App\Http\Controllers\Users\UsersController@show');
        $api->put('users/{uuid}', 'App\Http\Controllers\Users\UsersController@update');
        $api->patch('users/{uuid}', 'App\Http\Controllers\Users\UsersController@partialUpdate');
        $api->delete('users/{uuid}', 'App\Http\Controllers\Users\UsersController@destroy');
    });
});



