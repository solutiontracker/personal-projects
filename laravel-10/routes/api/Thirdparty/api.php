<?php
//Event center api,s
Route::group(['namespace' => 'Thirdparty', 'prefix' => 'thirdparty', 'as' => 'thirdparty-'], function () {

    //Mailchimp
    Route::group(['namespace' => 'Mailchimp', 'prefix' => 'mailchimp', 'as' => 'mailchimp-'], function () {
        Route::get('delete/rejection/denylist', ['as' => 'delete-rejection-denylist', 'uses' => 'RejectEmailController@delete_rejection_denylist']);
    });
    
    //Mailchimp
    Route::group(['namespace' => 'Wordpress', 'prefix' => 'wp', 'as' => 'wp-'], function () {
        Route::post('send-cr7-pdf-email', ['as' => 'send-cr7-pdf-email', 'uses' => 'UtilityController@SendPdfEmail']);
    });

   


});