<?php
Route::get('/qr-code', ['as' => 'qr-code', 'uses' => 'ApiController@getQrCode']);

Route::group(['middleware' => ['valid-ip-address']], function () {
    Route::get('/get-table-data/{table}', ['as' => 'get-table-data', 'uses' => 'DbSeedingController@getTableData']);
});