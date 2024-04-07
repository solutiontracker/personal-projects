<?php
Route::group(['middleware' => ['auth.organizer.token', 'valid.interface.language'], 'namespace' => 'Wizard', 'as' => 'wizard-'], function () {
    
    //Event Routes
    Route::group(['prefix' => 'event', 'as' => 'event-'], function () {
        Route::post('copy-template/{id}', ['as' => 'copy-template', 'uses' => 'EventController@copy']);
    });

    //Eventsite billing orders routes
    Route::group(['prefix' => 'billing', 'as' => 'billing-'], function () {
        Route::post('sendOrderEmail/{order_id}', ['as' => 'orders', 'uses' => 'EventsiteBillingOrderController@sendOrderEmail']);
        Route::get('order-summary/{order_id}', ['as' => 'order-summary', 'uses' => 'EventsiteBillingOrderController@orderSummary']);
        Route::post('send-order/{order_id}', ['as' => 'send-order', 'uses' => 'EventsiteBillingOrderController@sendOrder']);
        Route::get('send-order-pdf/{order_id}', ['as' => 'send-order-pdf', 'uses' => 'EventsiteBillingOrderController@sendOrderPdf']);
        Route::post('send-ean/{order_id}', ['as' => 'send-ean', 'uses' => 'EventsiteBillingOrderController@sendEan']);
    });
    
});

Route::group(['middleware' => ['auth:organizer', 'valid.interface.language'], 'namespace' => 'Wizard', 'as' => 'wizard-'], function () {

    Route::group(['middleware' => ['auth:organizer']], function () {

        //Event Routesss
        Route::group(['prefix' => 'event', 'as' => 'event-'], function () {
            Route::post('listing/{page?}', ['as' => 'listing', 'uses' => 'EventController@listing']);
            Route::post('store', ['as' => 'store', 'uses' => 'EventController@store']);
            Route::post('update/{id}', ['as' => 'update', 'uses' => 'EventController@update']);
            Route::delete('destroy/{id}', ['as' => 'destroy', 'uses' => 'EventController@destroy']);
            Route::get('fetch/{id}', ['as' => 'fetch', 'uses' => 'EventController@fetchEvent'])->middleware('valid.langauge');
            Route::match(["GET", "POST"], 'templates', ['as' => 'templates', 'uses' => 'EventController@templates']);
            Route::post('copy/{id}', ['as' => 'copy', 'uses' => 'EventController@copy']);
            Route::post( 'sub_admins/{id}', ['as' => 'sub-admins', 'uses' => 'EventController@getSubAdmins']);
            Route::post( 'unassign/admin/{id}', ['as' => 'unassign-admins', 'uses' => 'EventController@UnassignAdmins']);
            Route::post( 'assign/admin/{id}', ['as' => 'assign-admins', 'uses' => 'EventController@assignAdmins']);
        });

        //General Api,s
        Route::group(['prefix' => 'general', 'as' => 'general-'], function () {
            Route::get('metadata/{param?}', ['as' => 'metadata', 'uses' => 'GeneralController@getMetadata']);
        });

        /***********validate event by header data*************/
        Route::group(['middleware' => ['valid.event']], function () {

            //dashboard
            Route::post('dashboard', ['as' => 'dashboard', 'uses' => 'EventController@dashboard']);

            //Event Routes
            Route::group(['prefix' => 'event', 'as' => 'event-'], function () {
                Route::get('progress', ['as' => 'progress', 'uses' => 'EventController@progress']);
            });

            //Attendee Routes
            Route::group(['prefix' => 'attendee', 'as' => 'attendee-'], function () {
                Route::post('listing/{page?}', ['as' => 'listing', 'uses' => 'AttendeeController@listing']);
                Route::post('store', ['as' => 'store', 'uses' => 'AttendeeController@store']);
                Route::put('update/{id}', ['as' => 'update', 'uses' => 'AttendeeController@update']);
                Route::delete('destroy/{id}', ['as' => 'destroy', 'uses' => 'AttendeeController@destroy']);
                Route::get('export', ['as' => 'export', 'uses' => 'AttendeeController@export']);
                Route::post('invitations/{page?}', ['as' => 'invitations', 'uses' => 'AttendeeController@invitations']);
                Route::post('app-invitations/{page?}', ['as' => 'app-invitations', 'uses' => 'AttendeeController@app_invitations']);
                Route::post('app-invitations-not-sent/{page?}', ['as' => 'app-invitations-not-sent', 'uses' => 'AttendeeController@app_invitations_not_sent']);
                Route::post('not-registered/{page?}', ['as' => 'not-registered', 'uses' => 'AttendeeController@not_registered']);
                Route::post('not-attendees-list/{page?}', ['as' => 'not-attendees-list', 'uses' => 'AttendeeController@not_attendees_list']);
                Route::match(['post', 'put'], 'save-invitation/{id?}', ['as' => 'save-invitation', 'uses' => 'AttendeeController@save_invitation']);
                Route::match(['delete'], 'destroy-invitation/{id?}', ['as' => 'destroy-invitation', 'uses' => 'AttendeeController@destroy_invitation']);
                Route::put('invitation-process', ['as' => 'invitation-process', 'uses' => 'AttendeeController@invitation_process']);
                Route::put('invitation-template', ['as' => 'invitation-template', 'uses' => 'AttendeeController@invitation_template']);
                Route::put('send-invitation', ['as' => 'send-invitation', 'uses' => 'AttendeeController@send_invitation']);
                Route::post('registration/invitations/reminder-log/{page?}', ['as' => 'registration-invitations-reminder-log', 'uses' => 'AttendeeController@registration_invitations_reminder_log']);
                Route::post('app/invitations/reminder-log/{page?}', ['as' => 'app-invitations-reminder-log', 'uses' => 'AttendeeController@app_invitations_reminder_log']);
                Route::match(['get', 'put'], 'settings', ['as' => 'settings', 'uses' => 'AttendeeController@settings']);
                Route::get('invitations/export/{type}', ['as' => 'export-invitations', 'uses' => 'AttendeeController@export_invitations'])->where('type', 'registration-invitations|not-registered|not-attending-list');
                Route::put('send-test-email', ['as' => 'send-test-email', 'uses' => 'AttendeeController@send_test_email']);
                Route::match(['post'], 'attendee-type', ['as' => 'attendee-type', 'uses' => 'AttendeeController@attendee_type']);
                Route::get('invitations-stats', ['as' => 'invitations-stats', 'uses' => 'AttendeeController@invitations_stats']);
            });

            //Eventinfo Routes
            Route::group(['prefix' => 'event-info', 'as' => 'event-info-'], function () {
                Route::post('{cms}/listing/{id?}', ['as' => 'listing', 'uses' => 'EventInfoController@listing'])->where('cms', 'general-info|practical-info|additional-info|information-pages');
                Route::post('{cms}/{type}/store', ['as' => 'store', 'uses' => 'EventInfoController@store'])->where('cms', 'general-info|practical-info|additional-info|information-pages')->where('type', 'menu|link|page|folder');
                Route::post('{cms}/{type}/update/{id}', ['as' => 'update', 'uses' => 'EventInfoController@update'])->where('cms', 'general-info|practical-info|additional-info|information-pages')->where('type', 'menu|link|page|folder');
                Route::delete('{cms}/{type}/destroy/{id}', ['as' => 'destroy', 'uses' => 'EventInfoController@destroy'])->where('cms', 'general-info|practical-info|additional-info|information-pages')->where('type', 'menu|link|page|folder');
                Route::put('{cms}/update/order', ['as' => 'update-order', 'uses' => 'EventInfoController@update_order'])->where('cms', 'general-info|practical-info|additional-info|information-pages');
            });

            //SubRegistration Routes
            Route::group(['prefix' => 'sub-registration', 'as' => 'sub-registration-'], function () {
                Route::post('listing', ['as' => 'listing', 'uses' => 'SubRegistrationController@listing']);
                Route::get('question/types', ['as' => 'question-types', 'uses' => 'SubRegistrationController@question_types']);
                Route::post('questions', ['as' => 'questions', 'uses' => 'SubRegistrationController@questions']);
                Route::post('question/store/{id?}', ['as' => 'question-store', 'uses' => 'SubRegistrationController@question_store']);
                Route::put('question/update/{id}/{question_id}', ['as' => 'question-update', 'uses' => 'SubRegistrationController@question_update']);
                Route::delete('question/destroy/{id}/{question_id}', ['as' => 'question-destroy', 'uses' => 'SubRegistrationController@question_destroy']);
                Route::delete('question/option/destroy/{id}', ['as' => 'question-option-destroy', 'uses' => 'SubRegistrationController@question_option_destroy']);
                Route::delete('question/option/matrix/destroy/{id}', ['as' => 'question-matrix-option-destroy', 'uses' => 'SubRegistrationController@question_matrix_option_destroy']);
                Route::put('question/update/order', ['as' => 'update-question-order', 'uses' => 'SubRegistrationController@update_question_order']);
                Route::post('question/results', ['as' => 'question-results', 'uses' => 'SubRegistrationController@question_results']);
                Route::match(['get', 'put'], 'settings', ['as' => 'settings', 'uses' => 'SubRegistrationController@settings']);
                Route::match(['get', 'put'], 'update-module-setting', ['as' => 'update-module-setting', 'uses' => 'SubRegistrationController@update_module_setting']);
            });

            //Survey Routes
            Route::group(['prefix' => 'survey', 'as' => 'survey-'], function () {
                Route::post('listing/{page?}', ['as' => 'listing', 'uses' => 'SurveyController@listing']);
                Route::post('store', ['as' => 'store', 'uses' => 'SurveyController@store']);
                Route::put('update/{id}', ['as' => 'update', 'uses' => 'SurveyController@update']);
                Route::delete('destroy/{id}', ['as' => 'destroy', 'uses' => 'SurveyController@destroy']);
                Route::get('fetch/{id}', ['as' => 'fetch', 'uses' => 'SurveyController@fetch']);
                Route::get('group/{id}', ['as' => 'fetch-group', 'uses' => 'SurveyController@getGroups']);
                Route::post('group/{id}', ['as' => 'assign-group', 'uses' => 'SurveyController@assignGroups']);
                Route::post('status/{id}', ['as' => 'update-status', 'uses' => 'SurveyController@updateStatus']);
                Route::post('questions/{id}', ['as' => 'questions', 'uses' => 'SurveyController@questions']);
                Route::put('/update-status/{id}', ['as' => 'update-status', 'uses' => 'SurveyController@updateStatus']);
                Route::post('question/store/{id}', ['as' => 'question-store', 'uses' => 'SurveyController@question_store']);
                Route::put('question/update/{id}/{question_id}', ['as' => 'question-update', 'uses' => 'SurveyController@question_update']);
                Route::delete('question/destroy/{id}/{question_id}', ['as' => 'question-destroy', 'uses' => 'SurveyController@question_destroy']);
                Route::delete('question/option/destroy/{id}', ['as' => 'question-option-destroy', 'uses' => 'SurveyController@question_option_destroy']);
                Route::delete('question/option/matrix/destroy/{id}', ['as' => 'question-matrix-option-destroy', 'uses' => 'SurveyController@question_matrix_option_destroy']);
                Route::put('question/update/order', ['as' => 'update-question-order', 'uses' => 'SurveyController@update_question_order']);
                Route::put('question/update-status/{id}', ['as' => 'update-question-status', 'uses' => 'SurveyController@update_question_status']);
                Route::get('clear-results/{id}', ['as' => 'clear-result', 'uses' => 'SurveyController@clearSurveyResults']);
                Route::get('question/clear-results/{id}', ['as' => 'clear-result', 'uses' => 'SurveyController@clearQuestionResults']);
                Route::get('/export_single_result/{id}', ['as' => 'export-single-result', 'uses' => 'SurveyController@surveySingleResultExport']);
                Route::get('/export_by_points', ['as' => 'export-by-points', 'uses' => 'SurveyController@surveyResultByPointsExport']);
                Route::get('/get_leaderboard/{id}', ['as' => 'export-by-points', 'uses' => 'SurveyController@surveyGetLeaderBoard']);
            });

            // EventSite Settings Routes
            Route::group(['prefix' => 'eventsite-settings', 'as' => 'eventsite-settings-'], function () {
                Route::match(['post', 'put'], 'eventSiteTopMenus', ['as' => 'eventSiteTopMenus', 'uses' => 'EventSiteSettingController@eventSiteTopMenus']);
            });

            //General Api,s
            Route::group(['prefix' => 'general', 'as' => 'general-'], function () {
                Route::get('interface-labels/{lang?}', ['as' => 'interface.labels', 'uses' => 'GeneralController@getInterfaceLabels']);
                Route::post('import/{entity}', ['as' => 'import', 'uses' => 'GeneralController@import'])->where('entity', 'attendees|program|attendee-invites');
                Route::post('upload-file', ['as' => 'upload-file', 'uses' => 'GeneralController@upload_file']);
            });

            //Program Routes
            Route::group(['prefix' => 'program', 'as' => 'program-'], function () {
                Route::post('listing/{page?}', ['as' => 'listing', 'uses' => 'ProgramController@listing']);
                Route::match(['post', 'put'], 'assign-speakers', ['as' => 'assign-speakers', 'uses' => 'ProgramController@assignSpeakers']);
                Route::post('store', ['as' => 'store', 'uses' => 'ProgramController@store']);
                Route::put('update/{id}', ['as' => 'update', 'uses' => 'ProgramController@update']);
                Route::delete('destroy/{id}', ['as' => 'destroy', 'uses' => 'ProgramController@destroy']);
                Route::get('all', ['as' => 'all', 'uses' => 'ProgramController@getAllPrograms']);
                Route::get('download-pdf', ['as' => 'download-pdf', 'uses' => 'ProgramController@download_pdf']);
            });

            //Map Routes
            Route::group(['prefix' => 'map', 'as' => 'map-'], function () {
                Route::get('fetch', ['as' => 'fetch', 'uses' => 'MapController@fetch']);
                Route::post('store', ['as' => 'store', 'uses' => 'MapController@store']);
                Route::post('update/{id}', ['as' => 'update', 'uses' => 'MapController@update']);
            });

            // Event Settings Routes
            Route::group(['prefix' => 'event-settings', 'as' => 'event-settings-'], function () {
                Route::get('disclaimer', ['as' => 'disclaimer', 'uses' => 'EventSettingController@getDisclaimer']);
                Route::get('gdpr-disclaimer', ['as' => 'gdpr-disclaimer', 'uses' => 'EventSettingController@getGdprDisclaimer']);
                Route::put('disclaimer/update', ['as' => 'disclaimer', 'uses' => 'EventSettingController@updateDisclaimer']);
                Route::put('gdpr-disclaimer/update', ['as' => 'gdpr-disclaimer', 'uses' => 'EventSettingController@updateGdprDisclaimer']);
                Route::post('branding', ['as' => 'branding', 'uses' => 'EventSettingController@branding']);
                Route::match(['post', 'put'], 'modules', ['as' => 'modules', 'uses' => 'EventSettingController@modules']);
                Route::match(['post', 'put'], 'modules/setting', ['as' => 'modules-setting', 'uses' => 'EventSettingController@modules_setting']);
                Route::get('module/{module}', ['as' => 'module', 'uses' => 'EventSettingController@getModule']);
            });

            //Hotel Routes
            Route::group(['prefix' => 'hotel', 'as' => 'hotel-'], function () {
                Route::post('listing/{page?}', ['as' => 'listing', 'uses' => 'HotelController@listing']);
                Route::post('store', ['as' => 'store', 'uses' => 'HotelController@store']);
                Route::put('update/{id}', ['as' => 'update', 'uses' => 'HotelController@update']);
                Route::delete('destroy/{id}', ['as' => 'destroy', 'uses' => 'HotelController@destroy']);
                Route::put('updateHotelPriceSetting', ['as' => 'update-hotel-price-setting', 'uses' => 'HotelController@updateHotelPriceSetting']);
                Route::get('getHotelPriceSetting', ['as' => 'get-hotel-price-setting', 'uses' => 'HotelController@getHotelPriceSetting']);
                Route::put('sorting', ['as' => 'sorting', 'uses' => 'HotelController@sorting']);
                Route::get('export', ['as' => 'export', 'uses' => 'HotelController@export']);
                Route::post('bookings/{page?}', ['as' => 'bookings', 'uses' => 'HotelController@bookings']);
            });

            //Registration Routes
            Route::group(['prefix' => 'registration', 'as' => 'registration-'], function () {
                Route::get('listing/{alias?}', ['as' => 'listing', 'uses' => 'RegistrationController@listing']);
                Route::put('update/{alias?}', ['as' => 'update', 'uses' => 'RegistrationController@update']);
            });

            //Template Routes
            Route::group(['prefix' => 'template', 'as' => 'template-'], function () {
                Route::match(['put', 'get'], '/edit/{id}', ['as' => 'listing', 'uses' => 'TemplateController@update']);
                Route::get('listing', ['as' => 'listing', 'uses' => 'TemplateController@listing']);
                Route::post('/logs/{template_id}/{page}', ['as' => 'logs', 'uses' => 'TemplateController@logs']);
                Route::get('/view/history/{id}', ['as' => 'view-history', 'uses' => 'TemplateController@view_history']);
            });

            //Eventsite Banner Routes
            Route::group(['prefix' => 'eventsite-banner', 'as' => 'eventsite-banner-'], function () {
                Route::delete('destroy/{id}', ['as' => 'destroy', 'uses' => 'EventSiteBannerController@destroy']);
            });

            //Alert Routes
            Route::group(['prefix' => 'alert', 'as' => 'alert-'], function () {
                Route::post('listing', ['as' => 'listing', 'uses' => 'AlertController@listing']);
                Route::match(['put', 'post'], 'store', ['as' => 'store', 'uses' => 'AlertController@store']);
                Route::match(['put', 'get'], 'update/{id}', ['as' => 'update', 'uses' => 'AlertController@update']);
                Route::delete('destroy/{id}', ['as' => 'destroy', 'uses' => 'AlertController@destroy']);
            });

            //Eventsite billing orders routes
            Route::group(['prefix' => 'billing', 'as' => 'billing-'], function () {
                Route::post('orders/{page?}', ['as' => 'orders', 'uses' => 'EventsiteBillingOrderController@orders']);
                Route::post("waiting-list-orders/{page?}", ["as" => "waiting-list-orders", "uses" => "EventsiteBillingOrderController@waitingListOrders"]);
                Route::get("waiting-list-orders/send-offer/{orderId}", ["as" => "waiting-list-orders.sendOffer", "uses" => "EventsiteBillingOrderController@sendOffer"]);
                Route::get("waiting-list-orders/delete-order/{orderId}", ["as" => "waiting-list-orders.deleteOrder", "uses" => "EventsiteBillingOrderController@deleteOrder"]);
            });

            // Eventsite Routes
            Route::group(['prefix' => 'eventsite', 'as' => 'eventsite-'], function () {
                //billing Routes
                Route::group(['prefix' => 'billing', 'as' => 'billing-'], function () {
                    //payment setting routes
                    Route::match(['GET', 'PUT'], 'payment-providers', ['as' => 'payment-providers', 'uses' => 'EventSiteSettingController@paymentProviders']);
                    Route::match(['GET', 'PUT'], 'invoice-settings', ['as' => 'invoice-settings', 'uses' => 'EventSiteSettingController@invoiceSettings']);
                    Route::match(['GET', 'PUT'], 'ean-settings', ['as' => 'ean-settings', 'uses' => 'EventSiteSettingController@eanSettings']);
                    Route::match(['GET', 'PUT'], 'fik-settings', ['as' => 'fik-settings', 'uses' => 'EventSiteSettingController@fikSettings']);
                    Route::match(['GET', 'PUT'], 'payment-methods', ['as' => 'payment-methods', 'uses' => 'EventSiteSettingController@paymentMethods']);
                    Route::match(['GET', 'PUT'], 'purchase-policy', ['as' => 'purchase-policy', 'uses' => 'EventSiteSettingController@purchasePolicy']);

                    //Items routes
                    Route::post('items/listing', ['as' => 'item-listing', 'uses' => 'EventsiteBillingItemController@listing']);
                    Route::match(["GET", "PUT"], 'items/edit/{id}', ['as' => 'item-edit', 'uses' => 'EventsiteBillingItemController@edit']);
                    Route::put('items/create', ['as' => 'item-create', 'uses' => 'EventsiteBillingItemController@create']);
                    Route::delete('items/delete/{id}', ['as' => 'item-delete', 'uses' => 'EventsiteBillingItemController@destroy']);
                    Route::put('items/update-status/{id}', ['as' => 'item-update-status', 'uses' => 'EventsiteBillingItemController@updateItemStatus']);
                    Route::post('items/link-to-search', ['as' => 'item-link-to-search', 'uses' => 'EventsiteBillingItemController@linkToSearch']);
                    Route::put('items/update-order', ['as' => 'item-update-order', 'uses' => 'EventsiteBillingItemController@updateItemOrder']);

                    //Voucher routes
                    Route::post('voucher/listing/{page?}', ['as' => 'voucher-listing', 'uses' => 'EventsiteBillingVoucherController@listing']);
                    Route::match(["GET", "PUT"], 'voucher/edit/{id}', ['as' => 'voucher-edit', 'uses' => 'EventsiteBillingVoucherController@edit']);
                    Route::put('voucher/create', ['as' => 'voucher-create', 'uses' => 'EventsiteBillingVoucherController@create']);
                    Route::put('voucher/items/{id?}', ['as' => 'voucher-items', 'uses' => 'EventsiteBillingVoucherController@items']);
                    Route::delete('voucher/delete/{id}', ['as' => 'voucher-delete', 'uses' => 'EventsiteBillingVoucherController@destroy']);
                    Route::put('voucher/update-status/{id}', ['as' => 'voucher-update-status', 'uses' => 'EventsiteBillingVoucherController@updateVoucherStatus']);
                    Route::get('vouchers/export', ['as' => 'vouchers-export', 'uses' => 'EventsiteBillingVoucherController@export']);
                    Route::post('voucher/generate-code', ['as' => 'voucher-generate-code', 'uses' => 'EventsiteBillingVoucherController@generateCode']);

                    //Orders routes
                    Route::post('orders/listing/{page?}', ['as' => 'order-listing', 'uses' => 'EventsiteBillingOrderController@listing']);
                    Route::post('orders/export', ['as' => 'orders-export', 'uses' => 'EventsiteBillingOrderController@export']);
                    Route::post('orders/cancel/{id}', ['as' => 'order-cancel', 'uses' => 'EventsiteBillingOrderController@cancel_order']);
                    Route::post('orders/export-detail', ['as' => 'orders-export-detail', 'uses' => 'EventsiteBillingOrderController@export_detail']);
                });
            });

            // Documents Routes
            Route::group(['prefix' => 'directory', 'as' => 'directory-', 'middleware' => ['setEventTimezone']], function () {
                Route::post('/document/listing/{module}/{id}', ['as' => 'document-listing', 'uses' => 'DirectoryController@listing']);
                Route::post('/add/document/{module}', ['as' => 'add-document', 'uses' => 'DirectoryController@addDocument']);
                Route::post('/update/document/{module?}', ['as' => 'update-document', 'uses' => 'DirectoryController@updateDocument']);
                Route::delete('/destroy/document/{module}', ['as' => 'destroy-document', 'uses' => 'DirectoryController@destroyDocument']);
                Route::post('/upload/document/{module}', ['as' => 'upload-document', 'uses' => 'DirectoryController@uploadDocument']);
                Route::post('/rename/document/file/{module}', ['as' => 'rename-document-file', 'uses' => 'DirectoryController@renameDocumentFile']);
                Route::post('/schedule/document/{module}', ['as' => 'schedule-document', 'uses' => 'DirectoryController@scheduleDocument']);
                Route::post('/move/document/file/{module}', ['as' => 'move-document-file', 'uses' => 'DirectoryController@moveFile']);
                Route::post('/copy/document/file/{module}', ['as' => 'copy-document-file', 'uses' => 'DirectoryController@copyFile']);
                Route::get('/document/load-module-data/{module}', ['as' => 'document-load-module-data', 'uses' => 'DirectoryController@loadModuleData']);
                Route::get('/document/load-group-data', ['as' => 'document-load-group-data', 'uses' => 'DirectoryController@loadGroupData']);
            });
        });
        /***********End*************/

        /***********validate event by event id*************/
        Route::group(['middleware' => ['valid.app.event.id']], function () {
            //Survey Routes
            Route::group(['prefix' => 'survey', 'as' => 'survey-'], function () {
                Route::get('question/full-screen-projector/{event_id}/{id}', ['as' => 'full-screen-projector', 'uses' => 'SurveyController@fullScreenProjector']);
            });
        });
        /***********End*************/

        /***********no validate event*************/
        // user setting, later we move into user setting controller
        Route::group(['prefix' => 'user-settings', 'as' => 'event-settings-'], function () {
            Route::put('updateUserInterfaceLanguage', ['as' => 'modules-setting', 'uses' => 'EventSettingController@updateUserInterfaceLanguage']);
        });
        /***********End*************/

        /***********Organizer routes*************/
        Route::group(['prefix' => 'organizer', 'as' => 'organizer-'], function () {
            Route::match(["GET", "PUT"], 'profile', ['as' => 'profile', 'uses' => 'OrganizerController@profile']);
            Route::put('change-password', ['as' => 'change-password', 'uses' => 'OrganizerController@change_password']);
        });
    });

});

/***********Open routes*************/
Route::group(['as' => 'wizard-', 'namespace' => 'Wizard'], function () {
    // Documents Routes
    Route::group(['prefix' => 'directory', 'as' => 'directory-'], function () {
        Route::get('/download/document/file/{module}/{id}', ['as' => 'download-document-file', 'uses' => 'DirectoryController@downloadFile']);
    });
});
/***********End*************/

//Generic Routes
Route::put('update-column-status', ['as' => 'update-column-status', 'uses' => 'Controller@updateColumnStatus'])->middleware(['auth:organizer', 'valid.event']);

/**
 * Ios Notification Routes
 */
Route::group(['middleware' => ['auth.organizer.token'], 'namespace' => 'Api'], function () {
    //Event Routes
    Route::post('/send/ios/notification', ['as' => 'ios-notification', 'uses' => 'PushNotificationController@sendIosPushNotificaiton']);
    Route::post('/send/ios/chat/notification', ['as' => 'ios-chat-notification', 'uses' => 'PushNotificationController@sendIosChatPushNotificaiton']);
});