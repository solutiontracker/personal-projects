<?php
//Event center api,s
Route::group(['middleware' => ['auth.organizer.token'], 'namespace' => 'Api', 'as' => 'api-'], function () {

    //Jira api routes
    Route::group(['prefix' => 'jira', 'as' => 'jira-'], function () {
        Route::post('create/issue', ['as' => 'create-issue', 'uses' => 'JiraController@createIssue']);
    });

    //Program video routes
    Route::group(['prefix' => 'program-video', 'as' => 'program-video-'], function () {
        Route::post('start-vonage-broadcasting', ['as' => 'start-vonage-broadcasting', 'uses' => 'Program\VideoController@startVonageBroadcasting']);
        Route::post('stop-vonage-broadcasting', ['as' => 'stop-vonage-broadcasting', 'uses' => 'Program\VideoController@stopVonageBroadcasting']);
    });
    
    //billing
    Route::group(['prefix' => 'billing', 'as' => 'billing-'], function () {
        Route::post('cancel-order/{order_id}', ['as' => 'cancel-order', 'uses' => 'EventsiteBillingOrderController@cancelOrder']);
    });

});

//Thirdparty api,s
Route::group(['middleware' => ['api.auth'], 'namespace' => 'Api', 'as' => 'api-'], function () {
    //Event routes
    Route::group(['prefix' => 'event', 'as' => 'event-'], function () {
        Route::post('create', ['as' => 'create', 'uses' => 'EventController@create']);
    });
});

Route::group(['namespace' => 'Api', 'as' => 'api-'], function () {
    //Event specific routes
    Route::group(['middleware' => ['valid.api.event', 'api.auth', 'api.request.response'], 'prefix' => 'event/{event_id}'], function () {

        //Eventsite registration routes
        Route::group(['prefix' => 'registration'], function () {
            //Eventsite billing order routes
            Route::group(['as' => 'eventsite-billing-order-'], function () {
                Route::post('create/order', ['as' => 'create-order', 'uses' => 'EventsiteBillingOrderController@createOrder']);
            });
            
            //Item routes
            Route::group(['as' => 'eventsite-billing-item-'], function () {
                Route::post('items', ['as' => 'items', 'uses' => 'EventsiteBillingItemController@index']);
            });

            //Hotel routes
            Route::group(['as' => 'eventsite-billing-hotel-'], function () {
                Route::post('hotels', ['as' => 'hotels', 'uses' => 'EventsiteBillingHotelController@index']);
            });
        });
        
        //General Api,s
        Route::group(['prefix' => 'general', 'as' => 'general-'], function () {
            Route::get('metadata/{param}', ['as' => 'metadata', 'uses' => 'GeneralController@getMetadata']);
        });
    });
});