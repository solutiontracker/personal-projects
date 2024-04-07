<?php
Route::group(['middleware' => ['request.log'], 'prefix' => 'event/{slug}'], function () {
    Route::group(['middleware' => ['valid.app.event', 'virtual.app.json'], 'namespace' => 'Mobile', 'as' => 'mobile-'], function () {

        //Event Routes
        Route::group(['as' => 'event-'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'EventController@index']);
        });

        Route::group(['middleware' => ['auth:attendee']], function () {
            Route::group(['middleware' => ['validate.attendee']], function () {
                //Dashboard Routes
                Route::group(['prefix' => 'dashboard', 'as' => 'dashboard-'], function () {
                    Route::get('lobby', ['as' => 'lobby', 'uses' => 'DashboardController@lobby']);
                });

                //Program Routes
                Route::group(['prefix' => 'program', 'as' => 'program-'], function () {
                    Route::post('timetable', ['as' => 'timetable', 'uses' => 'ProgramController@timetable']);
                    Route::post('videos', ['as' => 'videos', 'uses' => 'ProgramController@videos']);
                });

                //CheckInOut Routes
                Route::group(['prefix' => 'check-in-out', 'as' => 'check-in-out-'], function () {
                    Route::get('/', ['as' => 'index', 'uses' => 'CheckInOutController@index']);
                    Route::post('/save', ['as' => 'save', 'uses' => 'CheckInOutController@save']);
                });

                //Setting Routes
                Route::group(['prefix' => 'setting', 'as' => 'setting-'], function () {
                    Route::post('/update/gdpr/{action}', ['as' => 'update-gdpr', 'uses' => 'SettingController@update_gdpr']);
                });

                //MyTurnlist Routes
                Route::group(['prefix' => 'myturnlist', 'as' => 'myturnlist-'], function () {
                    Route::post('/speaking-attendee', ['as' => 'speaking-attendee', 'uses' => 'MyTurnListController@speakingAttendee']);
                    Route::post('/streaming-actions', ['as' => 'streaming-actions', 'uses' => 'MyTurnListController@streamingActions']);
                });

                //Agora Routes
                Route::group(['prefix' => 'agora', 'as' => 'agora-'], function () {
                    Route::post('/{control}', ['as' => 'handle-control', 'uses' => 'Agora\AgoraController@handleControl']);
                });

                //Attendee Routes
                Route::group(['prefix' => 'attendee', 'as' => 'attendee-'], function () {
                    Route::post('/detail/{id}', ['as' => 'detail', 'uses' => 'AttendeeController@detail']);
                    Route::get('/profile', ['as' => 'profile', 'uses' => 'AttendeeController@profile']);
                });

                //Event Routes
                Route::group(['prefix' => 'event', 'as' => 'event-'], function () {
                    Route::post('camera/access/{camera}', ['as' => 'camera-access', 'uses' => 'EventController@cameraAccess']);
                });
            });
        });

        //Zoom.us Routes
        Route::group(['prefix' => 'zoom', 'as' => 'zoom-'], function () {
            Route::get('/join/{attendee_id}/{meeting_id}/{password}/{role}', ['as' => 'join-meeting', 'uses' => 'Zoom\ZoomController@join']);
        });

        //Agora Routes
        Route::group(['prefix' => 'agora', 'as' => 'agora-'], function () {
            Route::post('/create/token', ['as' => 'create-token', 'uses' => 'Agora\AgoraController@createToken']);
        });

        //Opentok Routes
        Route::group(['prefix' => 'opentok', 'as' => 'opentok-'], function () {
            Route::post('/create/token', ['as' => 'create-token', 'uses' => 'OpenTok\OpenTokController@createToken']);
            Route::post('/create/session', ['as' => 'create-session', 'uses' => 'OpenTok\OpenTokController@createSession']);

            //Recording opentok
            Route::post('/recording/start-recording', ['as' => 'start-recording', 'uses' => 'OpenTok\OpenTokController@startRecording']);
            Route::post('/recording/stop-recording', ['as' => 'stop-recording', 'uses' => 'OpenTok\OpenTokController@stopRecording']);
        });

    });
});
