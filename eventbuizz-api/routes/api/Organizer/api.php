<?php
Route::group(['namespace' => 'Organizer'], function () {

    //Content management
    Route::group(['prefix' => 'content-management', 'namespace' => 'ContentManagement', 'as' => 'content-management'], function () {
        Route::post('store', ['as' => 'store', 'uses' => 'PageBuilderController@contentStore']);
        Route::get('load/{id}', ['as' => 'load', 'uses' => 'PageBuilderController@contentLoad']);
    });
    
    Route::group(['prefix' => 'form-builder', 'namespace' => 'FormBuilder', 'as' => 'form-builder'], function () {
        Route::post('createForm/{event_id}/{registration_form_id?}', ['as' => 'createForm', 'uses' => 'FormBuilderController@createForm']);
        Route::get('getForms/{event_id}/{registration_form_id?}', ['as' => 'getForms', 'uses' => 'FormBuilderController@getForms']);
        Route::post('getForm/{event_id}/{registration_form_id?}', ['as' => 'getForm', 'uses' => 'FormBuilderController@getForm']);
        Route::post('saveSection/{event_id}/{registration_form_id?}', ['as' => 'saveSection', 'uses' => 'FormBuilderController@saveSection']);                
        Route::post('saveSectionSort/{event_id}/{registration_form_id?}', ['as' => 'saveSectionSort', 'uses' => 'FormBuilderController@saveSectionSort']);                
        Route::post('addQuestion/{event_id}/{registration_form_id?}', ['as' => 'addQuestion', 'uses' => 'FormBuilderController@addQuestion']);
        Route::post('updateQuestion/{event_id}/{registration_form_id?}', ['as' => 'updateQuestion', 'uses' => 'FormBuilderController@updateQuestion']);
        Route::post('updateQuestionSection/{event_id}/{registration_form_id?}', ['as' => 'updateQuestionSection', 'uses' => 'FormBuilderController@updateQuestionSection']);
        Route::post('updateQuestionSort/{event_id}/{registration_form_id?}', ['as' => 'updateQuestionSort', 'uses' => 'FormBuilderController@updateQuestionSort']);
        Route::post('deleteSection/{event_id}/{registration_form_id?}', ['as' => 'deleteSection', 'uses' => 'FormBuilderController@deleteSection']);
        Route::post('deleteQuestion/{event_id}/{registration_form_id?}', ['as' => 'deleteQuestion', 'uses' => 'FormBuilderController@deleteQuestion']);
        Route::post('cloneQuestion/{event_id}/{registration_form_id?}', ['as' => 'cloneQuestion', 'uses' => 'FormBuilderController@cloneQuestion']);
        Route::post('cloneSection/{event_id}/{registration_form_id?}', ['as' => 'cloneSection', 'uses' => 'FormBuilderController@cloneSection']);
        Route::post('submitForm/{event_id}/{registration_form_id?}', ['as' => 'submitForm', 'uses' => 'FormBuilderController@submitForm']);
        Route::post('getFormAndResult/{event_id}/{registration_form_id?}', ['as' => 'getFormAndResult', 'uses' => 'FormBuilderController@getFormAndResult']);
        Route::post('saveFormSort/{event_id}/{registration_form_id?}', ['as' => 'saveFormSort', 'uses' => 'FormBuilderController@saveFormSort']);                
        Route::post('saveFormStatus/{event_id}/{registration_form_id?}', ['as' => 'saveFormStatus', 'uses' => 'FormBuilderController@saveFormStatus']);                
        Route::post('renameForm/{event_id}/{registration_form_id?}', ['as' => 'renameForm', 'uses' => 'FormBuilderController@renameForm']);                
        Route::post('copyForm/{event_id}/{registration_form_id?}', ['as' => 'copyForm', 'uses' => 'FormBuilderController@copyForm']);                
        Route::post('deleteForm/{event_id}/{registration_form_id?}', ['as' => 'deleteForm', 'uses' => 'FormBuilderController@deleteForm']);                
        Route::post('saveFormGlobal/{event_id}/{registration_form_id?}', ['as' => 'saveFormGlobal', 'uses' => 'FormBuilderController@saveFormGlobal']);                
    });
    
});