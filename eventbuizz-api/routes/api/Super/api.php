<?php
    Route::group(['namespace' => 'Super', 'as' => 'super-',], function () {

        Route::get('oauth2callback', ['as' => 'oauth2callback', 'uses' => 'AnalyticsController@oAuth2Callback']);
        Route::get('command1', ['as' => 'command1', 'uses' => 'AnalyticsController@command1']);
        Route::get('command2', ['as' => 'command2', 'uses' => 'AnalyticsController@command2']);
        
    });
