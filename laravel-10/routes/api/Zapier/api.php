<?php
//Authentication
Route::get('test/zapier', function(){
    return json_encode(['error'=> false]);
});

Route::group(['middleware' => ['auth:organizer'], 'prefix' => 'zapier', 'namespace' => 'Zapier', 'as' => 'zapier-',], function(){
    Route::get('attendees', ['as' => 'attendee-listing', 'uses' => 'AttendeeController@index']);
});
