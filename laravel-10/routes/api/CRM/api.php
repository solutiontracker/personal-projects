<?php
Route::group(['namespace' => 'CRM', 'as' => 'crm-',], function () {
    //Salesforce
    Route::group(['prefix' => 'salesforce', 'as' => 'salesforce-'], function () {

        //Authentications routes
        Route::get('login', '\Frankkessler\Salesforce\Controllers\SalesforceController@login_form');
        Route::get('callback', '\Frankkessler\Salesforce\Controllers\SalesforceController@process_authorization_callback');
        Route::get('test', 'Frankkessler\Salesforce\Controllers\SalesforceController@test_account');
    });

    //Microsoft dynamics

    //E-conomics
});
