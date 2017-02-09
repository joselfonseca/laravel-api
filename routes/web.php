<?php
Route::get('/', function(){
    return redirect('login');
});

Route::get('apidocs', function(){
    // aglio -i docs/api/blueprint/apidocs.apib --theme-variables Flatly --theme-template triple -o resources/views/apidocs.blade.php
    return view('apidocs');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

