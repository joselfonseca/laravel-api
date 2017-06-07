<?php

Route::get('/', 'HomeController@index');
Route::get('apidocs', 'ApiDocsController@index');
Auth::routes();

