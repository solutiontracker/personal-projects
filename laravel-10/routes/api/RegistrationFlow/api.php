<?php
Route::group(['middleware' => ['request.log'], 'prefix' => 'event/{slug}'], function () {
    Route::group(['middleware' => ['valid.registration.flow.event', 'registration.validate.order'], 'namespace' => 'RegistrationFlow', 'as' => 'registration-flow-'], function () {

        //Event Routes
        Route::group(['as' => 'event-'], function () {
            Route::get('/fetch-event', ['as' => 'fetch-event', 'uses' => 'EventController@index']);  
        });

        //Registration order Routes
        Route::group(['as' => 'order-'], function () {

            //Quick registration
            Route::get('/registration/autoregister/{ids}', ['as' => 'eventsite-autoregister', 'uses' => 'ManageAttendeeController@autoregister']);
            
            //Manage attendees
            Route::match(["put", "get"], '/registration/add-attendees', ['as' => 'add-attendees', 'uses' => 'ManageAttendeeController@addAttendees']);
            Route::match(["post", "get"], '/registration/attendee-personal-information/{order_id?}/{attendee_id?}', ['as' => 'attendee-personal-information', 'uses' => 'ManageAttendeeController@index']);
            Route::get('/registration/attendee/delete/{order_id}/{attendee_id}', ['as' => 'attendee-delete', 'uses' => 'ManageAttendeeController@deleteOrderAttendee']);
            Route::post('/registration/completed-attendee-iteration', ['as' => 'completed-attendee-iteration', 'uses' => 'ManageAttendeeController@completeAttendeeIteration']);
            Route::get('/registration/get-order-attendee-status/{order_id}/{attendee_id}', ['as' => 'get-order-attendee-status', 'uses' => 'ManageAttendeeController@getOrderAttendeeStatus']);
            Route::match(["post"], '/registration/validate-event-registration-code', ['as' => 'validate-event-registration-code', 'uses' => 'ManageAttendeeController@validateEventRegistrationCode']);

            //Manage items
            Route::match(["post", "get"], '/registration/items/{order_id}/{attendee_id}', ['as' => 'registration-items', 'uses' => 'ManageItemController@index'])->where('id', '[0-9]+');
            
            //Manage documents
            Route::get('/documents-data/{order_id}/{attendee_id}', ['as' => 'documents-items', 'uses' => 'ManageDocumentController@getInitialDataByRegFormId']);
            Route::delete('/documents/delete/{document_id}', ['as' => 'documents-item-delete', 'uses' => 'ManageDocumentController@deleteDocument']);
            Route::post('/documents/attach-types/{document_id}', ['as' => 'documents-item-attach-types', 'uses' => 'ManageDocumentController@attachTypes']);

            //Manage sub registration
            Route::match(["post", "get"], '/registration/sub-registration/{order_id}/{attendee_id}', ['as' => 'sub-registration', 'uses' => 'ManageSubRegistrationController@index'])->where('id', '[0-9]+');
            
            //Manage keywords
            Route::match(["post", "get"], '/registration/keywords/{order_id}/{attendee_id}', ['as' => 'keywords', 'uses' => 'ManageKeywordController@index'])->where('id', '[0-9]+');
            
            //Manage hotels
            Route::match(["post", "get"], '/registration/hotels/{order_id}/{attendee_id}', ['as' => 'hotels', 'uses' => 'ManageHotelController@index'])->where('order_id', '[0-9]+');
            Route::match(["post", "get"], '/registration/search-hotels/{order_id}/{attendee_id}', ['as' => 'search-hotels', 'uses' => 'ManageHotelController@searchHotels'])->where('order_id', '[0-9]+');
            Route::post('/registration/save-hotels/{order_id}/{attendee_id}', ['as' => 'save-hotels', 'uses' => 'ManageHotelController@saveHotels'])->where('order_id', '[0-9]+');
            Route::post('/registration/hotel/delete/{order_id}/{order_hotel_id}', ['as' => 'delete-hotel', 'uses' => 'ManageHotelController@delete'])->where('order_id', '[0-9]+');
            
            //Manage vouchers
            Route::post('/registration/apply-voucher/{order_id}', ['as' => 'apply-voucher', 'uses' => 'ManageVoucherController@index'])->where('order_id', '[0-9]+');
            Route::post('/registration/remove-voucher/{order_id}', ['as' => 'remove-voucher', 'uses' => 'ManageVoucherController@remove'])->where('order_id', '[0-9]+');
            
            //Order summary
            Route::match(["post", "get"], '/registration/order-summary/{order_id}', ['as' => 'summary', 'uses' => 'ManageOrderController@index'])->where('order_id', '[0-9]+');
            Route::post('/registration/submit-order/{order_id}', ['as' => 'submit-order', 'uses' => 'ManageOrderController@submitOrder'])->where('order_id', '[0-9]+');
            Route::get('/registration/add-to-calender/{order_id}', ['as' => 'add-to-calender', 'uses' => 'ManageOrderController@addToCalender'])->where('order_id', '[0-9]+');
            Route::post('/registration/cancel-waitinglist-order/{order_id}', ['as' => 'cancel-waitinglist-order', 'uses' => 'ManageOrderController@cancelWaitingListOrder']);
            Route::post('/registration/clone-order/{order_id}/{platform?}', ['as' => 'clone-order', 'uses' => 'ManageOrderController@cloneOrder'])->where('order_id', '[0-9]+');
            Route::get('/registration/order-invoice/{order_id}', ['as' => 'order-invoice', 'uses' => 'ManageOrderController@getOrderInvoice'])->where('order_id', '[0-9]+');
            Route::post('/registration/update-sale-type/{order_id}', ['as' => 'update-sale-type', 'uses' => 'ManageOrderController@updateSaleType'])->where('order_id', '[0-9]+');

            //Manage payments
            Route::match(["post", "get"], '/registration/payment-information/{order_id}', ['as' => 'payment-information', 'uses' => 'ManagePaymentController@index']);
            Route::match(["post"], '/registration/validate-cvr', ['as' => 'validate-cvr', 'uses' => 'ManagePaymentController@validateCvr']);
            Route::match(["post"], '/registration/validate-ean', ['as' => 'validate-ean', 'uses' => 'ManagePaymentController@validateEan']);
            Route::match(["post"], '/registration/validate-po-number', ['as' => 'validate-po-number', 'uses' => 'ManagePaymentController@validatePoNumber']);
        
            //Manage online payments
            Route::match(["post", "get"], '/registration/online-payment/{order_id}/{type}', ['as' => 'online-payment', 'uses' => 'ManagePaymentController@onlinePayment']);

            //Stripe
            Route::post('/registration/create-stripe-payment-intent/{order_id}', ['as' => 'create-stripe-payment-intent', 'uses' => 'ManagePaymentController@createStripePaymentIntent']);

            //Nets
            Route::match(["post", "get"], '/registration/nets-checkout/{order_id}', ['as' => 'nets-checkout', 'uses' => 'ManagePaymentController@netsCheckout']);
            Route::post('/registration/create-nets-payment-intent/{order_id}', ['as' => 'create-nets-payment-intent', 'uses' => 'ManagePaymentController@createNetsPaymentIntent']);
            
            //Quickpay
            Route::post('/registration/create-quickpay-payment-intent/{order_id}', ['as' => 'create-quickpay-payment-intent', 'uses' => 'ManagePaymentController@createQuickpayPaymentIntent']);

            //Bambora
            Route::match(["post", "get"], '/registration/bambora-checkout/{order_id}', ['as' => 'bambora-checkout', 'uses' => 'ManagePaymentController@bamboraCheckout']);
            Route::post('/registration/create-bambora-payment-intent/{order_id}', ['as' => 'create-bambora-payment-intent', 'uses' => 'ManagePaymentController@createBamboraPaymentIntent']);
        
            //Converge
            Route::match(["post", "get"], '/registration/converge-checkout/{order_id}', ['as' => 'converge-checkout', 'uses' => 'ManagePaymentController@convergeCheckout']);
            Route::post('/registration/create-converge-payment-intent/{order_id}', ['as' => 'create-converge-payment-intent', 'uses' => 'ManagePaymentController@createConvergePaymentIntent']);

            //Eventsite settings
            Route::post('/registration/eventsite-settings/eventsite-section-fields', ['as' => 'eventsite-section-fields', 'uses' => 'EventsiteSettingController@eventsiteSectionFields']);

        });

    });
});

Route::group(['prefix' => 'event/{slug}'], function () {
    Route::group(['middleware' => ['valid.registration.flow.event', 'registration.validate.order'], 'namespace' => 'RegistrationFlow', 'as' => 'registration-flow-'], function () {     
        Route::post('/documents/upload/{order_id}/{attendee_id}', ['as' => 'upload-document', 'uses' => 'ManageDocumentController@uploadDocument']);
    });
});



Route::group(['prefix' => 'webhook', 'namespace' => 'RegistrationFlow'], function () {

    //Manage payment webhooks
    Route::post('/stripe-ipn', ['as' => 'stripe-ipn', 'uses' => 'ManagePaymentWebhookController@stripe_ipn']);
    Route::post('/quickpay-ipn', ['as' => 'quickpay-ipn', 'uses' => 'ManagePaymentWebhookController@quickpay_ipn']);
    Route::post('/nets-ipn', ['as' => 'quickpay-ipn', 'uses' => 'ManagePaymentWebhookController@nets_ipn']);
    Route::get('/bambora-ipn/{event_id}/{order_id}', ['as' => 'quickpay-ipn', 'uses' => 'ManagePaymentWebhookController@bambora_ipn']);
    
});
