<?php

namespace App\Console\Commands\GoogleAnalytics;

use Illuminate\Console\Command;
use App\Mail\Email;

class GenerateAnalyticsProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:analyticsProperties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Analytics Properties and add them to requests Added';

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
    public function createProperty($analytics, $eventUrl, $eventName, $accountID)
    {
        try {
            $property = new \Google_Service_Analytics_Webproperty();
            $property->setName($eventName);
            $property->setWebsiteUrl($eventUrl);
            $property->setIndustryVertical('BUSINESS_AND_INDUSTRIAL_MARKETS');
            $newProperty = $analytics->management_webproperties->insert($accountID, $property);
            return $newProperty;
        } catch (\Google\Service\Exception $e) {
            $ex = json_decode($e->getMessage());
            $this->logInfo("(updatedAnalyticsAccounts) Service Error While creating property against ". $accountID, $e->getCode(), $ex->error->message);
            return $ex->error->message;
        } catch (\Google\Exception $e) {
            $ex = json_decode($e->getMessage());
            $this->logInfo("(updatedAnalyticsAccounts) Service Error While creating property against ". $accountID, $e->getCode(), $ex->error->message);
        }
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
    
    /**
     * Update Analytics Properties
     *
     * @return mixed
     */
    public function updatedAnalyticsAccounts()
    {
        $gmailAccounts =
        \App\Models\GoogleAnalyticsGmailAccount::where(function($q){
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
                'https://www.googleapis.com/auth/analytics.edit'
            );
            $client = new \Google_Client();
            $client->setAuthConfig(storage_path() . '/app/public/secrets/google_analytics_client_secret.json');
            $client->setRedirectUri(config("app.url") . '/api/v2/oauth2callback');
            $client->addScope($scopes);
            $client->setApprovalPrompt('force');
            $client->setAccessType('offline');
            $client->refreshToken($gmailAccount->refresh_token);
            $access_token = $client->getAccessToken();  
            // Set the access token on the client.
            $client->setAccessToken($access_token);
            $analytics = new \Google_Service_Analytics($client);
            try {
                $accounts = $analytics->management_accounts->listManagementAccounts();
            } catch (\Google\Service\Exception $e) {
                $ex = json_decode($e->getMessage());
                $this->logInfo("(updatedAnalyticsAccounts) Service Error While Fetching Accounts against ". $gmailAccount->email, $e->getCode(), $ex->error->message);
                
            } catch (\Google\Exception $e) {
                $ex = json_decode($e->getMessage());
                $this->logInfo("(updatedAnalyticsAccounts) Error While Fetching Accounts against ". $gmailAccount->email, $e->getCode(), $ex->error->message);
            }
            info('adding accounts');
            foreach($accounts->getItems() as $account){
               $accountExists = \App\Models\GoogleAnalyticsAccount::where("ga_account_id", $account->id)->where("ga_gmail_id", $gmailAccount->id)->first();
               if(!$accountExists){
                try {
                    $properties = $analytics->management_webproperties
                        ->listManagementWebproperties($account->id);              
                  } catch (\Google\Service\Exception $e) {
                    $ex = json_decode($e->getMessage());
                    $this->logInfo("(updatedAnalyticsAccounts) Service Error While Fetching againse against ". $gmailAccount->email . 'accountId ='. $account->id , $e->getCode(), $ex->error->message);               
                  } catch (\Google\Exception $e) {
                    $ex = json_decode($e->getMessage());
                    $this->logInfo("(updatedAnalyticsAccounts) Service Error While Fetching againse against ". $gmailAccount->email . 'accountId ='. $account->id, $e->getCode(), $ex->error->message);
                  }

                   \App\Models\GoogleAnalyticsAccount::create([
                       "ga_gmail_id" => $gmailAccount->id,
                       "ga_account_id" => $account->id,
                       "ga_account_name" => $account->name,
                       "status" => $gmailAccount->status,
                       "ga_properties_count" => count($properties->getItems()),
                   ]);
               } 
            }
            
            \App\Models\GoogleAnalyticsGmailAccount::where('id', $gmailAccount->id)
                ->update([
                    "ga_accounts_count" => count($accounts->getItems()),
                ]);   
        }    
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
        \App\Models\GoogleAnalyticsGmailAccount::where('ga_accounts_count', "=", 100)->where('status', 'active')->update([
            "status" => "inactive"
        ]);
        \App\Models\GoogleAnalyticsServiceAccount::where('ga_views_count', "=", 100)->where('status', 'active')->update([
            "status" => "inactive"
        ]);
        
        foreach ($analyticsRequests as $req) {
            $gaAccount = \App\Models\GoogleAnalyticsAccount::where('ga_properties_count', "<", 100)->where('status', 'active')->with(['gmail'])->first();
            $gaServiceAccount = \App\Models\GoogleAnalyticsServiceAccount::where('ga_views_count', "<", 100)->where('status', 'active')->first();
            if ($gaAccount && $gaServiceAccount) {
                $scopes = array(
                    'https://www.googleapis.com/auth/userinfo.email',
                    'https://www.googleapis.com/auth/userinfo.profile',
                    'https://www.googleapis.com/auth/analytics.manage.users',
                    'https://www.googleapis.com/auth/analytics.edit'
                );
                $client = new \Google_Client();
                $client->setAuthConfig(storage_path() . '/app/public/secrets/google_analytics_client_secret.json');
                $client->setRedirectUri(config("app.url") . '/api/v2/oauth2callback');
                $client->addScope($scopes);
                $client->setApprovalPrompt('force');
                $client->setAccessType('offline');
                $client->refreshToken($gaAccount->gmail->refresh_token);
                $access_token = $client->getAccessToken();
                // Set the access token on the client.
                $client->setAccessToken($access_token);
                $analytics = new \Google_Service_Analytics($client);
                // check property count
                try {
                    $properties = $analytics->management_webproperties
                        ->listManagementWebproperties($gaAccount->ga_account_id);        
                  } catch (\Google\Service\Exception $e) {
                    $ex = json_decode($e->getMessage());
                    $this->logInfo("1 Service Error While Fetching properties against email ". $gaAccount->gmail->email ."account ". $gaAccount->ga_account_id, $e->getCode(), $ex->error->message);
                  } catch (\Google\Exception $e) {
                    $ex = json_decode($e->getMessage());
                    $this->logInfo("1 Error While Fetching properties against email ". $gaAccount->gmail->email ."account ". $gaAccount->ga_account_id, $e->getCode(), $ex->error->message);
                  }
                if(count($properties->getItems()) < 100)
                {   
                    $eventUrl = config("app.eventcenter_url") . '/event/' . $req->event->url .'/detail';
                    // Create new property
                    $newProperty = $this->createProperty($analytics, $eventUrl, $req->event_name, $gaAccount->ga_account_id);
                    if($newProperty === "Error creating this entity. You have reached the maximum allowed entities of this type."){
                        $analyticsRequests->push($req);
                        // updating property count
                        \App\Models\GoogleAnalyticsAccount::where("id", $gaAccount->id)->update([
                            "ga_properties_count" => 100,
                            "status" => 'completed'
                        ]);
                        continue;
                    }
                    elseif (!$newProperty->id){
                            $this->logInfo("Something Went Terribly Wrong");
                            exit;
                    }
                    $profile = new \Google_Service_Analytics_Profile();
                    $profile->setName('All Web Site Data');
                    $profile->setTimezone("Europe/Copenhagen");
                    try {
                        $newProfile = $analytics->management_profiles->insert($gaAccount->ga_account_id, $newProperty->id, $profile);
                    } catch (\Google\Service\Exception $e) {
                        $ex = json_decode($e->getMessage());
                        $this->logInfo("Service Error While creating property against email ". $gaAccount->gmail->email ."account ". $gaAccount->ga_account_id, $e->getCode(), $ex->error->message);
                    } catch (\Google\Exception $e) { 
                        $ex = json_decode($e->getMessage());
                        $this->logInfo("Error While creating property against email ". $gaAccount->gmail->email ."account ". $gaAccount->ga_account_id, $e->getCode(), $ex->error->message);
                    }

                    // Adding User Ref

                    $userRef = new \Google_Service_Analytics_UserRef();
                    $userRef->setEmail($gaServiceAccount->service_email);

                    // Create the permissions object.
                    $permissions = new \Google_Service_Analytics_EntityUserLinkPermissions();
                    $permissions->setLocal(array('COLLABORATE', 'READ_AND_ANALYZE'));

                    // Create the view (profile) user link.
                    $link = new \Google_Service_Analytics_EntityUserLink();
                    $link->setPermissions($permissions);
                    $link->setUserRef($userRef);

                    // This request creates a new View (Profile) User Link.
                    try {
                    $analytics->management_profileUserLinks->insert($gaAccount->ga_account_id, $newProperty->id,
                        $newProfile->id, $link);
                    } catch (\Google\Service\Exception $e) {
                        $ex = json_decode($e->getMessage());
                        $this->logInfo("Service Error While Assiging Service Account against email ". $gaAccount->gmail->email ."account ". $gaAccount->ga_account_id . "property ". $newProperty->id , $e->getCode(), $ex->error->message);
                    } catch (\Google\Exception $e) {
                        $ex = json_decode($e->getMessage());
                        $this->logInfo("Error While Assinging Service Account against email ". $gaAccount->gmail->email ."account ". $gaAccount->ga_account_id . "property" . $newProperty->id, $e->getCode(), $ex->error->message);
                    }

                    // Updating event Analytics
                    $setting = \App\Models\EventSetting::where('event_id', '=', $req->event_code)
                        ->where('name', '=', 'google_analytics')->first();
                    if ($setting != null) { {
                            $setting->updated_at = \Carbon\Carbon::now();
                            $setting->value = $newProperty->id;
                            $setting->save();
                        }
                    } else {
                        $formInput['event_id'] = $req->event_code;
                        $formInput['name'] = 'google_analytics';
                        $formInput['value'] = $newProperty->id;
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
                        $setting->value = $newProfile->id;
                        $setting->save();
                    } else {
                        $formInput['event_id'] = $req->event_code;
                        $formInput['name'] = 'google_analytics_profile_id';
                        $formInput['value'] = $newProfile->id;
                        $formInput['created_at'] = \Carbon\Carbon::now();
                        $formInput['updated_at'] = \Carbon\Carbon::now();
                        \App\Models\EventSetting::create($formInput);
                    }

                    \App\Models\AnalyticsRequest::where('event_code', $req->event_code)->update([
                        "analytics_email" => $gaServiceAccount->service_email,
                        "analytics_code" => $newProperty->id,
                        "profile_id" => $newProfile->id,
                        "gmail_email" => $gaAccount->gmail->email,
                        "status" => 1,
                    ]);
                    
                    // update perperty and view counts
                    $propertyCount = count($properties->getItems()) + 1;
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

                    $this->logInfo("Request Against event id " .$req->event_code . " was completed and with Property Id" . $newProperty->id . " and View Id " . $newProfile->id );

                }
                else{
                    $analyticsRequests->push($req);
                    // updating property count
                    $propertyCount = count($properties->getItems());
                    \App\Models\GoogleAnalyticsAccount::where("id", $gaAccount->id)->update([
                        "ga_properties_count" => $propertyCount
                    ]);
                }
            
            
            }
            else{
                if(!$gaAccount && !$gaServiceAccount){                    
                    $inqueueGaEmail = \App\Models\GoogleAnalyticsGmailAccount::where('ga_accounts_count', "<", 100)->where('status', 'inqueue')->first();
                    $inqueuegaServiceAccount = \App\Models\GoogleAnalyticsServiceAccount::where('ga_views_count', "<", 100)->where('status', 'inqueue')->first();
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
                    $inqueueGaEmail = \App\Models\GoogleAnalyticsGmailAccount::where('ga_accounts_count', "<", 100)->where('status', 'inqueue')->first();
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
                    $inqueuegaServiceAccount = \App\Models\GoogleAnalyticsServiceAccount::where('ga_views_count', "<", 100)->where('status', 'inqueue')->first();
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
}
