<?php
Route::group([ 'middleware' => ['auth.organizer.token'],  'prefix' => 'zoom', 'namespace' => 'Zoom', 'as' => 'zoom-',], function(){
    Route::post('meeting/create', ['as' => 'create-meeting', 'uses' => 'MeetingController@create']);
});
