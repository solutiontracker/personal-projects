<?php
use Google\Analytics\Admin\V1alpha\AnalyticsAdminServiceClient;
use Google\Analytics\Admin\V1alpha\Property;
use Google\Analytics\Admin\V1alpha\PropertyType;
use Google\Analytics\Admin\V1alpha\IndustryCategory;
use Google\Analytics\Admin\V1alpha\AccessBinding;
use Google\Analytics\Admin\V1alpha\DataStream;
use Google\Analytics\Admin\V1alpha\DataStream\DataStreamType;
use Google\Analytics\Admin\V1alpha\DataStream\WebStreamData;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use GuzzleHttp\Client;

Route::get('/', function () {
});

Route::get('/heartbeat', function () {
    echo "Server is up and running";
});

//Utilities
Route::get('/assign-attendees-to-parent/{id}', ['uses' => 'UtilitiesController@assignAttendeesToParent']);
Route::get('/update-active-orders-date/{event_id}', ['uses' => 'UtilitiesController@updateActiveOrdersData']);
Route::get('/assigned-hotel-rooms/{event_id}', ['uses' => 'UtilitiesController@assignedHotelRooms']);
Route::get('/update-order-company-detail-info', ['uses' => 'UtilitiesController@updateOrderCompanyDetailInfo']);
Route::get('/create-invoice/{mode?}/{order_number?}/{order_prefix?}', ['uses' => 'UtilitiesController@createInvoicesFromExcelOrders']);
Route::get('/agenda-subregistration-verification/{event_id}', ['uses' => 'UtilitiesController@agendaSubregistrationVerification']);
Route::get('/correction-agenda-info-value', ['uses' => 'UtilitiesController@correctionAgendaInfoValue']);
Route::get('/orders-phone-corrections', ['uses' => 'UtilitiesController@orderPhoneConrrections']);
Route::get('/order/hotel/room/assign-person/{event_id}', ['uses' => 'UtilitiesController@assignPersonsToOrderHotelRooms']);
Route::get('import-fields/{initial?}/{end?}', ['uses' => 'UtilitiesController@importAttendeeSpeakerFields']);
Route::get('/add-theme-layouts/{start_event_id}/{end_event_id}', ['uses' => 'UtilitiesController@createEventlayoutSectionsThemeModulevariations']);
Route::get('/update-theme-layouts/{type}/{start_event_id?}/{end_event_id?}', ['uses' => 'UtilitiesController@updateLayoutSection']);
Route::get('/update-module-variation-option', ['uses' => 'UtilitiesController@updateModuleVariationOptions']);


Route::get('agora/analytics', function(){
   $ga = new \App\Helpers\Agora\AnalyticsAPI();
   echo $ga->syncCallAnalytics();
});

Route::get('testing', ['as' => 'metadata', 'uses' => 'Controller@index']);

Auth::routes(['register' => false]);

Route::get('/web/autologin/{token}', 'Auth\Organizer\LoginController@autoLoginWeb');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/salesforce/object/describe', function(){
    //change Custom.php to, .'/services/data/v50.0/'.$uri;
    $result = Salesforce::custom()->get('sobjects/Lead/describe/');
    dump($result);
});

Route::get('salesforce/sync', function() {
    $sf = new \App\Helpers\SalesForce\SalesForceHelper();
    $sf->syncAttendees();
});

Route::get('salesforce/refresh', function() {

    $base_uri = 'https://login.salesforce.com/services/oauth2/token';

    $http = new Client();

    $response = $http->post($base_uri, [
        'form_params' => [
            'grant_type' => 'refresh_token',
            'refresh_token' => "5Aep861bHJ7ML5f5OvYrYdQO0mhJm0j1XU6k3IGQVOcLyfwu2TsdUoxOsnWkZpkFZ8AWalK1Biu.Mr3tCr6lXnD",
            'client_id' =>  env('sf_consumer_key'),
            'client_secret' =>  env('sf_consumer_secret'),
        ],
    ]);

    dump($response->getBody()->getContents());

});

Route::get('ga4/accounts', function(){

    $scopes = array(
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile',
        'https://www.googleapis.com/auth/analytics.manage.users',
        'https://www.googleapis.com/auth/analytics.manage.users.readonly',
        'https://www.googleapis.com/auth/analytics.edit'
    );

    $client = new \Google_Client();

    $client->setAuthConfig(storage_path() . '/app/public/secrets/google_test_client.json');

    $client->setRedirectUri(config("app.url") . '/api/v2/oauth2callback');

    $client->addScope($scopes);
    
    $client->setApprovalPrompt('force');

    $client->setAccessType('offline');

    $client->refreshToken("1//03v4dvtj4BHP_CgYIARAAGAMSNwF-L9IrgtIvy-k-me9Fy5NTWk7U-6FE4O9-sIJ6umegtj50O0Xo2rJpXTDMn8YOq7hYjyUeX5M");

    $access_token = $client->getAccessToken();  

    $config_key_file_path = json_decode(file_get_contents(storage_path() . '/app/public/secrets/google_test_client.json'), true);

    $config_key = isset($config_key_file_path['installed']) ? 'installed' : 'web';
    
    $client = new AnalyticsAdminServiceClient( [
        'credentials' => Google\ApiCore\CredentialsWrapper::build( [
            'scopes'  => [
                'https://www.googleapis.com/auth/analytics',
                'openid',
                'https://www.googleapis.com/auth/analytics.readonly',
            ],
            'keyFile' => [
                'type'          => 'authorized_user',
                'client_id'     => $config_key_file_path[$config_key]['client_id'],
                'client_secret' => $config_key_file_path[$config_key]['client_secret'],
                'refresh_token' => $access_token['refresh_token']
            ],
        ] ),
    ]);

    $filter = "parent:accounts/219505943";

    $properties = $client->listProperties($filter, ['pageSize'=>2000]);
});

