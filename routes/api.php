<?php

use Illuminate\Support\Facades\Route;

Route::get('ping', 'Api\PingController@index');

Route::get('assets/{uuid}/render', 'Api\Assets\RenderFileController@show');

Route::post('register', 'Api\Auth\RegisterController@store');
Route::post('passwords/reset', 'Api\Auth\PasswordsController@store');
Route::put('passwords/reset', 'Api\Auth\PasswordsController@update');

Route::group(['middleware' => ['auth:api']], function () {
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'Api\Users\UsersController@index');
        Route::post('/', 'Api\Users\UsersController@store');
        Route::get('/{uuid}', 'Api\Users\UsersController@show');
        Route::put('/{uuid}', 'Api\Users\UsersController@update');
        Route::patch('/{uuid}', 'Api\Users\UsersController@update');
        Route::delete('/{uuid}', 'Api\Users\UsersController@destroy');
    });

    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', 'Api\Users\RolesController@index');
        Route::post('/', 'Api\Users\RolesController@store');
        Route::get('/{uuid}', 'Api\Users\RolesController@show');
        Route::put('/{uuid}', 'Api\Users\RolesController@update');
        Route::patch('/{uuid}', 'Api\Users\RolesController@update');
        Route::delete('/{uuid}', 'Api\Users\RolesController@destroy');
    });

    Route::get('permissions', 'Api\Users\PermissionsController@index');

    Route::group(['prefix' => 'me'], function () {
        Route::get('/', 'Api\Users\ProfileController@index');
        Route::put('/', 'Api\Users\ProfileController@update');
        Route::patch('/', 'Api\Users\ProfileController@update');
        Route::put('/password', 'Api\Users\ProfileController@updatePassword');
    });

    Route::group(['prefix' => 'assets'], function () {
        Route::post('/', 'Api\Assets\UploadFileController@store');
    });
});
