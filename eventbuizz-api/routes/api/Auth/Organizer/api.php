<?php
Route::group(['middleware' => ['guest', 'valid.interface.language'], 'namespace' => 'Auth', 'as' => 'wizard-'], function () {
    Route::group(['prefix' => 'auth', 'namespace' => 'Organizer', 'as' => 'auth-'], function () {
        Route::post('login', 'LoginController@login');
        Route::get('auto-login/{token?}', 'LoginController@autoLogin');
        Route::post('register', 'RegisterController@register');
        Route::post('password/email', ['as' => 'password-email', 'uses' => 'ForgotPasswordController@sendResetLinkEmail']);
        Route::post('password/reset', ['as' => 'reset', 'uses' => 'ResetPasswordController@reset']);
    });
});

Route::group(['middleware' => ['auth:organizer'], 'namespace' => 'Auth', 'as' => 'wizard-'], function () {
    Route::group(['prefix' => 'auth', 'namespace' => 'Organizer', 'as' => 'auth-'], function () {
        Route::post('logout', 'LoginController@logout');
    });
});
