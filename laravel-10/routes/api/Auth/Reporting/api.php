<?php
Route::group(['prefix' => 'auth/reporting', 'namespace' => 'Auth\ReportingAgent', 'middleware' => ['valid.interface.language']], function () {
    Route::group(['middleware' => ['guest']], function () {
        Route::post('login', 'LoginController@login');
        Route::post('/forgot-password-send-code', 'LoginController@forgotPassword');
        Route::post('/verify-forgot-password-code', 'LoginController@verifyResetPasswordCode');
        Route::post('/reset-password', 'LoginController@resetPassword');
    });
    Route::group(['middleware' => ['auth:reporting-agent']], function () {
        Route::post('/logout', 'LoginController@logout');
    });
});