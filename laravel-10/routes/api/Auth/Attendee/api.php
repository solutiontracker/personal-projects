<?php
Route::group(['middleware' => ['request.log'], 'prefix' => 'event/{slug}'], function () {
    Route::group(['middleware' => ['guest:attendee', 'valid.app.event', 'virtual.app.json'], 'namespace' => 'Auth', 'as' => 'attendee-'], function () {
        Route::group(['prefix' => 'auth', 'namespace' => 'Attendee', 'as' => 'auth-'], function () {
            Route::post('login', 'LoginController@login');
            Route::match(['get', 'post'], 'verification/{id}', 'LoginController@verification');
            Route::post('password/email', ['as' => 'password-email', 'uses' => 'ForgotPasswordController@sendResetLinkEmail']);
            Route::post('password/reset', ['as' => 'reset', 'uses' => 'ResetPasswordController@reset']);
            Route::match(['get', 'post', 'put'], 'cpr-login', 'LoginController@cprLogin');
            Route::match(['put'], 'cpr-verification', 'LoginController@cprVerification');
        });
    });

    Route::group(['middleware' => ['auth:attendee'], 'namespace' => 'Auth', 'as' => 'attendee-'], function () {
        Route::group(['prefix' => 'auth', 'namespace' => 'Attendee', 'as' => 'auth-'], function () {
            Route::post('logout', 'LoginController@logout');
        });
    });
});
