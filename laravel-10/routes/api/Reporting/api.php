<?php
    Route::group(['namespace' => 'Reporting', 'as' => 'reporting-', 'prefix' => 'reporting', 'middleware' => ['valid.interface.language']], function () {
        Route::group(['middleware' => ['auth:reporting-agent']], function () {
            Route::post('events/filters', ['as' => 'events', 'uses' => 'ReportingAgentController@getReportingAgentfilters']);
            Route::post('events', ['as' => 'events', 'uses' => 'ReportingAgentController@getReportingAgentEvents']);
            Route::post('events/{event_id}/statsAndOrders', ['as' => 'event-stats', 'uses' => 'ReportingAgentController@agentEventStatsAndOrders']);
            Route::post('events/{event_id}/formBaseStats', ['as' => 'event-stats', 'uses' => 'ReportingAgentController@getFormBasedTicketingStats']);
            // export routes
            Route::post('export-orders', ['as' => 'events-order-export', 'uses' => 'ReportingAgentController@reportingAgentExportOrders']);
            Route::post('/export-event-orders/{event_id}', ['as' => 'events-order-export', 'uses' => 'ReportingAgentController@reportingAgentExportEventOrders']);
        });
    });


