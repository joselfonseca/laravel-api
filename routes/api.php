<?php

use Illuminate\Http\Request;

Route::get('ping', function(){
    return [
        'status' => 'ok',
        'timestamp' => \Carbon\Carbon::now()
    ];
});

Route::group(['middleware' => 'auth:api'], function() {
    Route::resource('users', 'Users\UsersController');
});



