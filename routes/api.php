<?php

Route::get('ping', function(){
    return [
        'status' => 'ok',
        'timestamp' => \Carbon\Carbon::now()
    ];
});

Route::group(['middleware' => ['auth:api'], ], function () {
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'Api\Users\UsersController@index');
        Route::post('/', 'Api\Users\UsersController@store');
        Route::get('/{uuid}', 'Api\Users\UsersController@show');
        Route::put('/{uuid}', 'Api\Users\UsersController@update');
        Route::patch('/{uuid}', 'Api\Users\UsersController@partialUpdate');
        Route::delete('/{uuid}', 'Api\Users\UsersController@destroy');
    });
});




