<?php
Route::group(['namespace' => 'WebApp', 'as' => 'web-app', 'prefix' => 'webapp'], function () {
    Route::post('{event_id}/programs', ['as' => 'programs', 'uses' => 'ProgramController@index']);
    Route::post('{event_id}/agendas_by_tracks', ['as' => 'programs', 'uses' => 'ProgramController@agendas_by_tracks']);
    Route::post('{event_id}/agendas_by_tracks_detail/{track_id}', ['as' => 'programs', 'uses' => 'ProgramController@agendas_by_tracks_detail']);
    Route::post('{event_id}/track_agendas_listing/{track_id}', ['as' => 'programs', 'uses' => 'ProgramController@track_agendas_listing']);
});



?>