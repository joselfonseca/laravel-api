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
        $api->resource('users', 'App\Http\Controllers\Users\UsersController');
    });
});



