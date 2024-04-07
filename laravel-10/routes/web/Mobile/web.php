<?php
//Program section
Route::group(['prefix' => 'program', 'namespace' => 'Mobile'], function () {

    //Video section
    Route::group(['prefix' => 'video'], function () {
        Route::get('ivs/stream/{program_id}/{code}', 'ProgramController@ivsStream');
        Route::get('on-demand-stream/{program_id}/{code}', 'ProgramController@onDemandStream');
    });
    
});