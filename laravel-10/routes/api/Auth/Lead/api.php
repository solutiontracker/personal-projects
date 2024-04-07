<?php
Route::group(['middleware' => ['valid.lead-event'], 'prefix' => 'auth/lead', 'namespace' => 'Auth\Lead'], function () {
    Route::group(['middleware' => ['guest']], function () {
        Route::post('register-lead-user', 'RegisterController@store');
        Route::post('login', 'LoginController@login');
        Route::post('/verify-verification-code', 'LoginController@verifyVerificationCode');
        Route::post('/send-verification-code-to', 'LoginController@sendVerificationCodeTo');
        Route::post('/resend-verification-code', 'LoginController@resendVerificationCode');
        Route::post('/forgot-password-send-code', 'LoginController@forgotPassword');
        Route::post('/verify-forgot-password-code', 'LoginController@verifyforgotPasswordCode');
        Route::post('/reset-password', 'LoginController@resetPassword');
    });
    Route::group(['middleware' => ['auth:attendee,lead-user']], function () {
        Route::post('/logout', 'LoginController@logout');
    });
});




