<?php
use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Helpers\DynamicsCRM\DynamicsContactHelper;
use App\Models\AddAttendeeLog;
use App\Models\DynamicsToken;

Route::get('/cron/salesforce', function () {
    \Artisan::call("salesforce:syncAttendee");
});

Route::get('/cron/dynamics', function () {
    \Artisan::call("dynamics:syncAttendee");
});

Route::get('/cron/attendees/{id}', function ($id) {
    $attendees = AttendeeRepository::getAttendeeLog($id);
    dump($attendees);
});

Route::group(['middleware' => ['auth']], function () {

    /**
     * Salesforce Routes
     */
    Route::get('salesforce/login', 'Auth\Salesforce\LoginController@login');
    Route::get('salesforce/callback', 'Auth\Salesforce\LoginController@callback');

    /**
     * Dynamics CRM Routes
     */
    Route::get('dynamics/login', 'Auth\CRMDynamics\LoginController@login');
    Route::get('dynamics/callback', 'Auth\CRMDynamics\LoginController@callback');
});

/**
 * Dynamcis API Testing routes.
 */

Route::get('dynamics/get', function() {
    $tokens = DynamicsToken::all();
    $dm = new \App\Helpers\DynamicsCRM\DynamicsHelper();
    $dm->getTokenFromRefreshToken(233);
});

Route::get('send/notifis', function(){
    return \Illuminate\Support\Facades\Artisan::call('send:ios_notifications');
});

Route::get('salesforce/create', function() {
    $tokens = SalesforceToken::all();
    $sfContactHelper = new SalesForceContactHelper();

    foreach ($tokens as $token) {
        $organizer_id = $token->user_id;
        setSalesforceConfigUser($organizer_id);

        $attendees = AttendeeRepository::getAttendeeLog($organizer_id);

        foreach ($attendees as $attendee) {
            $success = $sfContactHelper->upsert($attendee->toArray());
            $attendeeLog = AddAttendeeLog::find($attendee->log_id);
            if($success !== false){
                $attendeeLog->status = 1;
            }else{
                $attendeeLog->status = 2;
            }

            $attendeeLog->save();
        }
    }
});

Route::get('log/get', function(){
    $logs = \App\Eventbuizz\Repositories\AttendeeRepository::getAttendeeLog(6);
    dump($logs);
});