<?php 
Route::group(['middleware' => ['guest'], 'prefix' => 'zapier', 'namespace' => 'Auth', 'as' => 'zapier-'], function () {
    Route::group(['prefix' => 'auth', 'namespace' => 'Zapier', 'as' => 'auth-'], function () {
        Route::post('login', ['as' => 'post-login', 'uses' => 'LoginController@login']);
        Route::get('login', ['as' => 'get-login', 'uses' => 'LoginController@showLoginForm']);
    });
});

Route::group(['middleware' => ['auth'], 'prefix' => 'zapier', 'namespace' => 'Auth', 'as' => 'zapier-'], function () {
    Route::group(['prefix' => 'auth', 'namespace' => 'Zapier', 'as' => 'auth-'], function () {
        Route::post('logout', ['as' => 'logout', 'uses' => 'LoginController@logout']);
    });
});

