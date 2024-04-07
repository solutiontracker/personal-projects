<?php
    Route::group(['middleware' => ['gzip'], 'namespace' => 'Lead', 'as' => 'lead-', 'prefix' => 'lead'], function () {
        Route::group(['middleware' => ['valid.lead-event']], function () {
            Route::get('event-detail/{event_id}', ['as' => 'event-detail', 'uses' => 'EventController@getEventDetails']);
            Route::get('event-settings/{event_id}', ['as' => 'event-settings', 'uses' => 'EventController@getEventSettings']);
            Route::get('settings/{event_id}', ['as' => 'settings', 'uses' => 'LeadController@getLeadSettings']);

            Route::group(['middleware' => ['auth:attendee,lead-user']], function () {
                Route::post('contact-person/attached-sponsors-exhibitors', ['as' => 'contact-person-sponsor-exhibitors', 'uses' => 'LeadController@getContactPersonSponsorsExhibitors']);
                Route::post('scanned-lead-attendee-info', ['as' => 'user-info', 'uses' => 'LeadController@getScannedLeadAttendeeInfo']);
                Route::post('contact-person/profile-data', ['as' => 'contact-person-profile-data', 'uses' => 'LeadController@getContactPersonProfileData']);
                Route::post('sync-leads-from-device', ['as' => 'sync-leads-from-device', 'uses' => 'LeadController@syncLeadsFromDevice']);
                Route::post('get-leads', ['as' => 'get-leads', 'uses' => 'LeadController@getLeads']);
                Route::post('contact-person/get-profile-leader-board', ['as' => 'get-profile-leader-board', 'uses' => 'LeadController@getProfileLeaderBoard']);
            });
        });
    });


