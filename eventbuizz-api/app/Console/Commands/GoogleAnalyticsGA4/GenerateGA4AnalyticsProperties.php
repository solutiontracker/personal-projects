<?php

namespace App\Console\Commands\GoogleAnalyticsGA4;

use Illuminate\Console\Command;
use App\Mail\Email;
use Google\Analytics\Admin\V1alpha\AnalyticsAdminServiceClient;
use Google\Analytics\Admin\V1alpha\Property;
use Google\Analytics\Admin\V1alpha\PropertyType;
use Google\Analytics\Admin\V1alpha\IndustryCategory;
use Google\Analytics\Admin\V1alpha\AccessBinding;
use Google\Analytics\Admin\V1alpha\DataStream;
use Google\Analytics\Admin\V1alpha\DataStream\DataStreamType;
use Google\Analytics\Admin\V1alpha\DataStream\WebStreamData;
use Google\ApiCore\ApiException;


class GenerateGA4AnalyticsProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:gA4AnalyticsProperties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate GA4 Analytics Properties and add them to requests Added';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {  
        $analyticsRequests = \App\Models\AnalyticsRequest::with('organizer')->where('status',0)->whereHas('event',function ($query){
		    return $query->wherenull('deleted_at');
        })->get();

        // update account status
        \App\Models\GoogleAnalyticsGmailAccount::where('ga_accounts_count', "=", 2000)->where('status', 'active')->update([
            "status" => "inactive"
        ]);
        \App\Models\GoogleAnalyticsServiceAccount::where('ga_views_count', "=", 2000)->where('status', 'active')->update([
            "status" => "inactive"
        ]);
        
        foreach ($analyticsRequests as $req) {
            $gaAccount = \App\Models\GoogleAnalyticsAccount::where('ga_properties_count', "<", 2000)->where('status', 'active')->with(['gmail'])->first();
            $gaServiceAccount = \App\Models\GoogleAnalyticsServiceAccount::where('ga_views_count', "<", 2000)->where('status', 'active')->first();
            if ($gaAccount && $gaServiceAccount) {
                $scopes = array(
                    'https://www.googleapis.com/auth/userinfo.email',
                    'https://www.googleapis.com/auth/userinfo.profile',
                    'https://www.googleapis.com/auth/analytics.manage.users',
                    'https://www.googleapis.com/auth/analytics.manage.users.readonly',
                    'https://www.googleapis.com/auth/analytics.edit',
                );
    
                $client = new \Google_Client();
                $client->setAuthConfig(storage_path() . '/app/public/secrets/google_analytics_client_secret.json');
                $client->setRedirectUri(config("app.url") . '/api/v2/oauth2callback');
                $client->addScope($scopes);
                $client->setApprovalPrompt('force');
                $client->setAccessType('offline');
                $client->refreshToken($gaAccount->gmail->refresh_token);
                $access_token = $client->getAccessToken(); 
    
                // // Set the access token on the client.
                // $client->setAccessToken($access_token);
    
    
    
                $config_key_file_path = json_decode(file_get_contents(storage_path() . '/app/public/secrets/google_analytics_client_secret.json'), true);
    
                $config_key = isset($config_key_file_path['installed']) ? 'installed' : 'web';
                
                $analyticsAdminServiceClient = new AnalyticsAdminServiceClient( [
                    'credentials' => \Google\ApiCore\CredentialsWrapper::build( [
                        'scopes'  => $scopes,
                        'keyFile' => [
                            'type'          => 'authorized_user',
                            'client_id'     => $config_key_file_path[$config_key]['client_id'],
                            'client_secret' => $config_key_file_path[$config_key]['client_secret'],
                            'refresh_token' => $access_token['refresh_token']
                        ],
                    ] ),
                ]);

                // check property count
                try {
                    $properties =  $analyticsAdminServiceClient->listProperties("parent:accounts/".$gaAccount->ga_account_id, ['pageSize'=> 2000]);     
                } catch (\Google\ApiCore\ApiException $e) {
                    $ex = json_decode($e->getMessage());
                    $this->logInfo("1 Service Error While Fetching properties against email ". $gaAccount->gmail->email ."account ". $gaAccount->ga_account_id, $e->getCode(), $ex->error->message);
                } 

                if($properties !== null && (iterator_count($properties) < 2000))
                {   
                    $eventUrl = config("app.eventcenter_url") . '/event/' . $req->event->url .'/detail';
                    // Create new property
                    $newProperty = $this->createProperty($analyticsAdminServiceClient, $req->event_name, $gaAccount->ga_account_id);
                    if($newProperty == 429){
                        $analyticsRequests->push($req);
                        // updating property count
                        \App\Models\GoogleAnalyticsAccount::where("id", $gaAccount->id)->update([
                            "ga_properties_count" => 2000,
                            "status" => 'completed'
                        ]);
                        continue;
                    }
                    elseif (!$newProperty->name){
                            $this->logInfo("Something Went Terribly Wrong");
                            exit;
                    }

                    $newPropertyAccessBinding = $this->createAccessbinding($analyticsAdminServiceClient, $gaServiceAccount->service_email, $newProperty);

                    $newPropertyDataStream = $this->createDataStream($analyticsAdminServiceClient, $eventUrl, $newProperty);


                    // Updating event Analytics
                    $setting = \App\Models\EventSetting::where('event_id', '=', $req->event_code)
                        ->where('name', '=', 'google_analytics')->first();
                    if ($setting != null) { {
                            $setting->updated_at = \Carbon\Carbon::now();
                            $setting->value = $newPropertyDataStream->webStreamData->measurementId;
                            $setting->save();
                        }
                    } else {
                        $formInput['event_id'] = $req->event_code;
                        $formInput['name'] = 'google_analytics';
                        $formInput['value'] = $newPropertyDataStream->webStreamData->measurementId;
                        $formInput['created_at'] = \Carbon\Carbon::now();
                        $formInput['updated_at'] = \Carbon\Carbon::now();
                        \App\Models\EventSetting::create($formInput);
                    }

                    $setting = \App\Models\EventSetting::where('event_id', '=', $req->event_code)
                        ->where('name', '=', 'google_analytics_email')->first();
                    if ($setting != null) { {
                            $setting->updated_at = \Carbon\Carbon::now();
                            $setting->value = $gaServiceAccount->service_email;
                            $setting->save();
                        }
                    } else {
                        $formInput['event_id'] = $req->event_code;
                        $formInput['name'] = 'google_analytics_email';
                        $formInput['value'] = $gaServiceAccount->service_email;
                        $formInput['created_at'] = \Carbon\Carbon::now();
                        $formInput['updated_at'] = \Carbon\Carbon::now();
                        \App\Models\EventSetting::create($formInput);
                    }

                    $setting = \App\Models\EventSetting::where('event_id', '=', $req->event_code)->where('name', '=', 'gmail_email')->first();
                    if ($setting != null) {
                        $setting->updated_at = \Carbon\Carbon::now();
                        $setting->value = $gaAccount->gmail->email;
                        $setting->save();
                    } else {
                        $formInput['event_id'] = $req->event_code;
                        $formInput['name'] = 'gmail_email';
                        $formInput['value'] = $gaAccount->gmail->email;
                        $formInput['created_at'] = \Carbon\Carbon::now();
                        $formInput['updated_at'] = \Carbon\Carbon::now();
                        \App\Models\EventSetting::create($formInput);
                    }

                    $setting = \App\Models\EventSetting::where('event_id', '=', $req->event_code)->where('name', '=', 'google_analytics_profile_id')->first();
                    if ($setting != null) {
                        $setting->updated_at = \Carbon\Carbon::now();
                        $setting->value = $newPropertyDataStream->name;
                        $setting->save();
                    } else {
                        $formInput['event_id'] = $req->event_code;
                        $formInput['name'] = 'google_analytics_profile_id';
                        $formInput['value'] = $newPropertyDataStream->name;
                        $formInput['created_at'] = \Carbon\Carbon::now();
                        $formInput['updated_at'] = \Carbon\Carbon::now();
                        \App\Models\EventSetting::create($formInput);
                    }

                    \App\Models\AnalyticsRequest::where('event_code', $req->event_code)->update([
                        "analytics_email" => $gaServiceAccount->service_email,
                        "analytics_code" => $newProperty->name,
                        "profile_id" => $newPropertyDataStream->name,
                        "gmail_email" => $gaAccount->gmail->email,
                        "status" => 1,
                    ]);
                    
                    // update perperty and view counts
                    $propertyCount = ($properties ? iterator_count($properties) : 0) + 1;
                    $updateAnalyticsAccount  = [
                        "ga_properties_count" => $propertyCount  
                    ];
                    if($propertyCount === 100){
                        $updateAnalyticsAccount['status'] = "completed";
                    }
                    \App\Models\GoogleAnalyticsAccount::where("id", $gaAccount->id)->update($updateAnalyticsAccount);
                    $viewCount = $gaServiceAccount->ga_views_count + 1;
                    \App\Models\GoogleAnalyticsServiceAccount::where("id", $gaServiceAccount->id)->update([
                        "ga_views_count" => $viewCount
                    ]);

                    $this->logInfo("Request Against event id " .$req->event_code . " was completed and with Property Id" . $newProperty->name . " with webDataStream " . $newPropertyDataStream->name);

                }
                else{
                    $analyticsRequests->push($req);
                    // updating property count
                    $propertyCount = $properties ? iterator_count($properties) : 0;
                    \App\Models\GoogleAnalyticsAccount::where("id", $gaAccount->id)->update([
                        "ga_properties_count" => $propertyCount
                    ]);
                }
            
            
            }
            else{
                if(!$gaAccount && !$gaServiceAccount){                    
                    $inqueueGaEmail = \App\Models\GoogleAnalyticsGmailAccount::where('ga_accounts_count', "<", 2000)->where('status', 'inqueue')->first();
                    $inqueuegaServiceAccount = \App\Models\GoogleAnalyticsServiceAccount::where('ga_views_count', "<", 2000)->where('status', 'inqueue')->first();
                    if($inqueueGaEmail && $inqueuegaServiceAccount)
                    { 
                        \App\Models\GoogleAnalyticsGmailAccount::where('id', $inqueueGaEmail->id)->update(["status" =>'active']);
                        $this->updatedAnalyticsAccounts();                   
                        \App\Models\GoogleAnalyticsServiceAccount::where('id', $inqueuegaServiceAccount->id)->update(["status" => 'active']);
                        $analyticsRequests->push($req);
                        continue;
                    }
                    else{
                        $this->sendMail("No Analaytics Account and Service Account in queue found");
                        $this->logInfo("No Analaytics Account and Service Account in queue found");
                        break;
                    }
                }
                elseif(!$gaAccount){
                    $inqueueGaEmail = \App\Models\GoogleAnalyticsGmailAccount::where('ga_accounts_count', "<", 2000)->where('status', 'inqueue')->first();
                    if($inqueueGaEmail)
                    {
                        \App\Models\GoogleAnalyticsGmailAccount::where('id', $inqueueGaEmail->id)->update(["status" => 'active']);
                        $this->updatedAnalyticsAccounts();
                        $analyticsRequests->push($req);
                        continue;
                    } 
                    else{
                        $this->sendMail("No Analaytics Account in queue found");
                        $this->logInfo("No Analaytics Account in queue found");
                        break;
                    }
                }
                elseif(!$gaServiceAccount){
                    $inqueuegaServiceAccount = \App\Models\GoogleAnalyticsServiceAccount::where('ga_views_count', "<", 2000)->where('status', 'inqueue')->first();
                    if($inqueuegaServiceAccount)
                    {
                        \App\Models\GoogleAnalyticsServiceAccount::where('id', $inqueuegaServiceAccount->id)->update(["status" => 'active']);
                        $analyticsRequests->push($req);
                        continue;
                    }
                    else{
                        $this->sendMail("No Service Account in queue found");
                        $this->logInfo("No Service Account in queue found");
                        break;
                    }
                }
            }
            
        }
       $this->logInfo('Google Analytics Request completed!');
    }

    /**
     * Log info.
     *
     * @return void
     */
    public function logInfo($code_hint, $res_code = null, $response = null)
    {
        \App\Models\GoogleAnalyticsJobsLog::create([
            "job_type" => "Generate Properties",
            "code_hint" => $code_hint,
            "res_code" => $res_code,
            "response" =>  $response
        ]);
    }
    
    /**
     * Create property.
     *
     * @return mixed
     */
    public function createProperty($analyticsAdminServiceClient, $eventName, $accountID)
    {
        $property = (new Property())
        ->setDisplayName($eventName)
        ->setTimeZone("Europe/Copenhagen")
        ->setParent("accounts/".$accountID)
        ->setCurrencyCode("DKK")
        ->setAccount("accounts/".$accountID)
        ->setIndustryCategory(IndustryCategory::BUSINESS_AND_INDUSTRIAL_MARKETS)
        ->setPropertyType(PropertyType::PROPERTY_TYPE_ORDINARY);
    
        try {
            /** @var Property $response */
            $response = $analyticsAdminServiceClient->createProperty($property);
            // printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
            return json_decode($response->serializeToJsonString());
        } catch (\Google\ApiCore\ApiException $e) {
            $ex = json_decode($e->getMessage());
            $this->logInfo("(updatedAnalyticsAccounts) Service Error While creating property against ". $accountID, $e->getCode(), $ex->error->message);
            return $e->getCode();
        }
    }
    
    /**
     * createAccessbinding
     *
     * @param  mixed $analyticsAdminServiceClient
     * @param  mixed $service_email
     * @param  mixed $new_property
     * @return void
     */
    public function createAccessbinding($analyticsAdminServiceClient, $service_email, $new_property)
    {
        $accessBinding = (new AccessBinding())
                    ->setRoles(["predefinedRoles/analyst"])
                    ->setUser($service_email);
        // Call the API and handle any network failures.
        try {
            /** @var AccessBinding $response */
            $response = $analyticsAdminServiceClient->createAccessBinding($new_property->name, $accessBinding);
            // printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
            return json_decode($response->serializeToJsonString());
        } catch (\Google\ApiCore\ApiException $e) {
            $ex = json_decode($e->getMessage());
            $this->logInfo("(updatedAnalyticsAccounts) Service Error While creating accessbinding against ". $new_property->name, $e->getCode(), $ex->error->message);

        }
    }

    public function createDataStream($analyticsAdminServiceClient, $eventUrl, $new_property)
    {
        $webStreamData = (new WebStreamData())
        ->setDefaultUri($eventUrl);

        // Prepare any non-scalar elements to be passed along with the request.
        $dataStream = (new DataStream())
            ->setType(DataStreamType::WEB_DATA_STREAM)
            ->setDisplayName('All analytics data')
            ->setWebStreamData($webStreamData);

        // Call the API and handle any network failures.
        try {
            /** @var DataStream $response */
            $response = $analyticsAdminServiceClient->createDataStream($new_property->name, $dataStream);
            // printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
            return json_decode($response->serializeToJsonString());
        } catch (\Google\ApiCore\ApiException $e) {
            $ex = json_decode($e->getMessage());
            $this->logInfo("(updatedAnalyticsAccounts) Service Error While creating web data stream against ". $new_property->name, $e->getCode(), $ex->error->message);
        }
    }

        /**
     * Update Analytics Properties
     *
     * @return mixed
     */
    public function updatedAnalyticsAccounts()
    {
        $gmailAccounts = \App\Models\GoogleAnalyticsGmailAccount::where(function($q){
            $q->where('ga_accounts_count', null)
            ->orWhere("ga_accounts_count", "<", 100);
            })
            ->where('status', "active")
            ->whereNotNull('refresh_token')
            ->get();
    
            foreach ($gmailAccounts as $gmailAccount) {
               
                $scopes = array(
                    'https://www.googleapis.com/auth/userinfo.email',
                    'https://www.googleapis.com/auth/userinfo.profile',
                    'https://www.googleapis.com/auth/analytics.manage.users',
                    'https://www.googleapis.com/auth/analytics.manage.users.readonly',
                    'https://www.googleapis.com/auth/analytics.edit',
                );
    
                $client = new \Google_Client();
                $client->setAuthConfig(storage_path() . '/app/public/secrets/google_analytics_client_secret.json');
                $client->setRedirectUri(config("app.url") . '/api/v2/oauth2callback');
                $client->addScope($scopes);
                $client->setApprovalPrompt('force');
                $client->setAccessType('offline');
                $client->refreshToken($gmailAccount->refresh_token);
                $access_token = $client->getAccessToken(); 
    
                // // Set the access token on the client.
                // $client->setAccessToken($access_token);
    
    
    
                $config_key_file_path = json_decode(file_get_contents(storage_path() . '/app/public/secrets/google_analytics_client_secret.json'), true);
    
                $config_key = isset($config_key_file_path['installed']) ? 'installed' : 'web';
                
                $analyticsAdminServiceClient = new AnalyticsAdminServiceClient( [
                    'credentials' => \Google\ApiCore\CredentialsWrapper::build( [
                        'scopes'  => $scopes,
                        'keyFile' => [
                            'type'          => 'authorized_user',
                            'client_id'     => $config_key_file_path[$config_key]['client_id'],
                            'client_secret' => $config_key_file_path[$config_key]['client_secret'],
                            'refresh_token' => $access_token['refresh_token']
                        ],
                    ] ),
                ]);
    
    
                try {
                    $accounts = $analyticsAdminServiceClient->listAccounts();
                } catch (ApiException $e) {
                    $ex = json_decode($e->getMessage());
                    $this->logInfo("Service Error While Fetching Accounts against ". $gmailAccount->email, $e->getCode(), $ex->error->message);
                    
                } catch (\Google\Exception $e) {
                    $ex = json_decode($e->getMessage());
                    $this->logInfo("Error While Fetching Accounts against ". $gmailAccount->email, $e->getCode(), $ex->error->message);
                }
    
                if($accounts !== null && (iterator_count($accounts) > 0)){
                
                    foreach ($accounts as $key => $account) {
    
                        $account = json_decode($account->serializeToJsonString());
                        $account_id = explode('/', $account->name)[1];
                        $account_display_name = $account->displayName;
    
                        $accountExists = \App\Models\GoogleAnalyticsAccount::where("ga_account_id", $account_id)->where("ga_gmail_id", $gmailAccount->id)->first();
                        
                        if(!$accountExists){
                            try {
                                $properties = $analyticsAdminServiceClient->listProperties("parent:".$account->name);
                            } catch (ApiException $e) {
                                $ex = json_decode($e->getMessage());
                                $this->logInfo("Service Error While Fetching againse against ". $gmailAccount->email . 'accountId ='. $account_id , $e->getCode(), $ex->error->message);               
                            } catch (\Google\Exception $e) {
                                $ex = json_decode($e->getMessage());
                                $this->logInfo("Service Error While Fetching against ". $gmailAccount->email . 'accountId ='. $account_id, $e->getCode(), $ex->error->message);
                            }
    
                            \App\Models\GoogleAnalyticsAccount::create([
                                "ga_gmail_id" => $gmailAccount->id,
                                "ga_account_id" => $account_id,
                                "ga_account_name" => $account_display_name,
                                "status" => $gmailAccount->status,
                                "ga_properties_count" => $properties ? iterator_count($properties) : 0,
                            ]);
                        } 
                    }
    
                    \App\Models\GoogleAnalyticsGmailAccount::where('id', $gmailAccount->id)
                        ->update([
                            "ga_accounts_count" => $accounts ? iterator_count($accounts) : 0,
                        ]); 
                }
                else{
                    $this->sendMail("Unable to fetch Accounts against gmail = ".$gmailAccount->email);
                }
                 
            }
            
            $this->logInfo('Add Analytics Job Finished');
    }

    /**
     * Send Mail.
     *
     * @return mixed
     */
    public function sendMail($msg)
    {
        $data = array();
        $data['subject'] = "Google Analytics Something went wrong";
        $data['content'] ="Something went Wrong while generating google Analytics Properties/views message = ". $msg;
        $data['bcc'] = ['ki@eventbuizz.com', 'mms@eventbuizz.com'];
        $data['view'] = 'email.plain-text';
        \Mail::to('ida@eventbuizz.com')->send(new Email($data));
    }
    
}
