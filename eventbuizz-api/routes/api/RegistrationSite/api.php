<?php
    Route::group(['namespace' => 'RegistrationSite', 'as' => 'registration-site-',], function () {

        //Event Routes
        Route::group(['middleware' => ['valid.event.url' ],'prefix' => 'event/{slug}', 'as' => 'event-'], function () {

            Route::get('/', ['as' => 'fetch-event', 'uses' => 'EventController@index']);

            Route::post('meta-info', ['as' => 'meta-info', 'uses' => 'MetaInfoController@index']);

            /***********validate event*************/

            Route::group(['middleware' => ['route-based-labels']], function () {
                
                Route::get('eventsite/modules', ['as' => 'eventsite-modules', 'uses' => 'EventsiteModuleController@getModules']);
                
                Route::get('eventsite/settings', ['as' => 'event-modules', 'uses' => 'EventsiteSettingController@getEventsiteSettings']);
                
                Route::get('speakers', ['as' => 'event-speaker', 'uses' => 'AttendeeController@getSpeakers']);
                
                Route::get('speakers/{id}', ['as' => 'event-speaker-single', 'uses' => 'AttendeeController@getSpeaker']);
                
                Route::get('sponsors', ['as' => 'fetch-sponsors', 'uses' => 'SponsorController@getSponsors']);

                Route::get('sponsors-listing', ['as' => 'fetch-sponsors-listing', 'uses' => 'SponsorController@getSponsorsListing']);

                Route::get('sponsor-detail/{sponsor_id}', ['as' => 'fetch-sponsor-detail', 'uses' => 'SponsorController@getSponsorDetail']);
                
                Route::get('exhibitors', ['as' => 'fetch-exhibitors', 'uses' => 'ExhibitorController@getExhibitors']);

                Route::get('exhibitors-listing', ['as' => 'fetch-exhibitors-listing', 'uses' => 'ExhibitorController@getExhibitorsListing']);

                Route::get('exhibitor-detail/{exhibitor_id}', ['as' => 'fetch-exhibitor-detail', 'uses' => 'ExhibitorController@getExhibitorDetail']);
                
                Route::get('photos', ['as' => 'fetch-photos', 'uses' => 'PhotoController@getEventsitePhotos']);
                
                Route::get('videos', ['as' => 'fetch-videos', 'uses' => 'VideoController@getEventsiteVideos']);
                
                Route::get('settings', ['as' => 'fetch-settings', 'uses' => 'EventSettingController@getSettings']);
                
                Route::get('theme', ['as' => 'event-theme', 'uses' => 'EventController@getThemeModules']);
                
                Route::get('banner', ['as' => 'event-banner', 'uses' => 'EventController@getEventsiteTopBanner']);
                
                Route::get('map', ['as' => 'event-map', 'uses' => 'MapController@getMap']);

                Route::get('attendees', ['as' => 'event-attendees', 'uses' => 'AttendeeController@getAttendees']);
                
                Route::get('attendees/{id}', ['as' => 'event-attendees-single', 'uses' => 'AttendeeController@getAttendee']);
                
                Route::get('programs', ['as' => 'event-programs', 'uses' => 'ProgramController@getPrograms']);
                
                Route::get('program/detail/{id}', ['as' => 'event-programs', 'uses' => 'ProgramController@getProgram']);
                
                Route::post('program/search', ['as' => 'event-program-search', 'uses' => 'ProgramController@search']);
                
                Route::get('general_information/{id?}', ['as' => 'general-info', 'uses' => 'GeneralInformationController@getInformation']);
                
                Route::get('practical_information/{id?}', ['as' => 'practical-info', 'uses' => 'PracticalInformationController@getInformation']);
                
                Route::get('additional_information/{id?}', ['as' => 'additional-info', 'uses' => 'AdditionalInformationController@getInformation']);
                
                Route::get('general_information/page/{id}', ['as' => 'general-info', 'uses' => 'GeneralInformationController@getPage']);
                
                Route::get('practicalinformation/page/{id}', ['as' => 'practical-info', 'uses' => 'PracticalInformationController@getPage']);
                
                Route::get('additional_information/page/{id}', ['as' => 'additional-info', 'uses' => 'AdditionalInformationController@getPage']);

                Route::get('info_pages/page/{id}', ['as' => 'info-pages', 'uses' => 'EventSiteInformationPagesController@getInfoPage']);
                
                Route::get('alerts', ['as' => 'fetch-news', 'uses' => 'AlertsController@getAlerts']);
                
                Route::get('menu', ['as' => 'top-side-menu', 'uses' => 'EventsiteModuleController@topSideMenu']);
                
                Route::get('page/{id}', ['as' => 'page-builder-page', 'uses' => 'EventSiteCmsController@show']);
                
                Route::get('documents', ['as' => 'documents', 'uses' => 'EventSiteDocumentController@index']);

                Route::get('form-packages', ['as' => 'form-packages', 'uses' => 'EventSiteController@getManagePackagesListing']);

                //Event Routes
                Route::get('news', ['as' => 'fetch-news', 'uses' => 'NewsController@getNews']);
                
                Route::get('news/{id}/detail', ['as' => 'fetch-news-details', 'uses' => 'NewsController@details']);
                
                // Mailing List Form
                Route::get('getMailingListSubscriberForm/{ml_id}', ['as' => 'fetch-mailing-list-form', 'uses' => 'MailingListController@getMailingListSubscriberForm']);
                Route::post('subscribeToMailingList/{ml_id}', ['as' => 'subscribe-to-mailing-list', 'uses' => 'MailingListController@subscribeToMailingList']);
                
                //Event Attendee Account Routes
                Route::group(['middleware' => ['auth:attendee']], function () {
                    Route::get('attendee/profile', ['as' => 'fetch-attendee-profile', 'uses' => 'AttendeeController@attendeeProfile']);

                    Route::post('attendee/profile/update', ['as' => 'update-attendee-profile', 'uses' => 'AttendeeController@attendeeUpdateProfile']);

                    Route::get('/network-interest', ['as' => 'fetch-network-interest', 'uses' => 'NetworkInterestController@getNetworkInterest']);

                    Route::put('/update-network-interest', ['as' => 'update-network-interest', 'uses' => 'NetworkInterestController@updateNetworkInterest']);

                    Route::get('/newsletter-subscription/', ['as' => 'fetch-newsletter-subscription', 'uses' => 'AttendeeController@getNewsletterSubscription']);

                    Route::put('/update-newsletter-subscription/', ['as' => 'update-newsletter-subscription', 'uses' => 'AttendeeController@updateNewsletterSubscription']);

                    Route::get('/survey-listing/', ['as' => 'fetch-survey', 'uses' => 'SurveyController@getSurveyListing']);

                    Route::get('/survey-detail/{id}/', ['as' => 'fetch-survey-detail', 'uses' => 'SurveyController@getSurveyDetail']);

                    Route::post('/save-survey/{id}/', ['as' => 'save-survey-detail', 'uses' => 'SurveyController@saveSurveyDetail']);

                    Route::get('/sub-registration-after-login/', ['as' => 'sub-registration', 'uses' => 'SubRegistrationController@getSubRegistrationAfterLogin']);

                    Route::get('/my-sub-registration/', ['as' => 'sub-registration', 'uses' => 'SubRegistrationController@getMySubRegistration']);

                    Route::post('/save-sub-registration/', ['as' => 'save-sub-registration', 'uses' => 'SubRegistrationController@saveSubRegistration']);

                    Route::get('get-attendee-programs', ['as' => 'event-programs', 'uses' => 'ProgramController@getAttendeePrograms']);

                    Route::get('getInvoice', ['as' => 'attendees-get-invoice', 'uses' => 'EventSiteBillingController@getOrderInvoice']);

                    Route::post('cancel-registration', ['as' => 'attendees-get-invoice', 'uses' => 'EventSiteBillingController@cancel_order']);
                });
                
                Route::get('/billing-profile/{id}/', ['as' => 'billing-profile', 'uses' => 'AttendeeController@getbillingProfile']);

                Route::post('/validate-attendee/{verification_id}/{attendee_id}', ['as' => 'validate-attendee', 'uses' => 'AttendeeController@validateAttendee']);
            });

            Route::post('attendee-not-attending', ['as' => 'attendees-not-attending', 'uses' => 'AttendeeController@attendeeNotAttending']);

            Route::match(['get', 'post'], 'unsubscribe-attendee', ['as' => 'unsubscribe-attendee', 'uses' => 'EventSiteBillingController@unsubscribeAttendee']);
        });

        Route::get('/purgeCacheLabels/{event_id}', ['as' => 'purge-cache-labels', 'uses' => 'EventController@purgeCacheLabels']);
    });
