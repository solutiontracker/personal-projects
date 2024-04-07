<?php

namespace App\Console\Commands\GoogleAnalytics;

use Illuminate\Console\Command;
use App\Mail\Email;

class AddAnalyticsAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:analyticsAccounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Analytics Accounts and to db';

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
     * Log Info.
     *
     * @return void
     */
    public function logInfo($code_hint, $res_code = null, $response = null)
    {
        \App\Models\GoogleAnalyticsJobsLog::create([
            "job_type" => "Add Analytics",
            "code_hint" => $code_hint,
            "res_code" => $res_code,
            "response" =>  $response
        ]);
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
        $data['content'] ="Something went Wrong while Fetching google Analytics against active gmails message (". $msg . ")";
        $data['bcc'] = ['ki@eventbuizz.com', 'mms@eventbuizz.com'];
        $data['view'] = 'email.plain-text';
        \Mail::to('ida@eventbuizz.com')->send(new Email($data));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {      
        $this->logInfo("Add Analytics Job Started");
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
                $this->logInfo("Service Error While Fetching Accounts against ". $gmailAccount->email, $e->getCode(), $ex->error->message);
                
            } catch (\Google\Exception $e) {
                $ex = json_decode($e->getMessage());
                $this->logInfo("Error While Fetching Accounts against ". $gmailAccount->email, $e->getCode(), $ex->error->message);
            }
            if($accounts->totalResults > 0){
                foreach($accounts->getItems() as $account){
                    $accountExists = \App\Models\GoogleAnalyticsAccount::where("ga_account_id", $account->id)->where("ga_gmail_id", $gmailAccount->id)->first();
                    if(!$accountExists){
                        try {
                            $properties = $analytics->management_webproperties
                                ->listManagementWebproperties($account->id);
                        
                        } catch (\Google\Service\Exception $e) {
                            $ex = json_decode($e->getMessage());
                            $this->logInfo("Service Error While Fetching againse against ". $gmailAccount->email . 'accountId ='. $account->id , $e->getCode(), $ex->error->message);               
                        } catch (\Google\Exception $e) {
                            $ex = json_decode($e->getMessage());
                            $this->logInfo("Service Error While Fetching against ". $gmailAccount->email . 'accountId ='. $account->id, $e->getCode(), $ex->error->message);
                        }

                        \App\Models\GoogleAnalyticsAccount::create([
                            "ga_gmail_id" => $gmailAccount->id,
                            "ga_account_id" => $account->id,
                            "ga_account_name" => $account->name,
                            "status" => $gmailAccount->status,
                            "ga_properties_count" => $properties ? count($properties->getItems()) : 0,
                        ]);
                    } 
                }
                
                \App\Models\GoogleAnalyticsGmailAccount::where('id', $gmailAccount->id)
                    ->update([
                        "ga_accounts_count" => count($accounts->getItems()),
                    ]); 
            }
            else{
                $this->sendMail("Unable to fetch Accounts against gmail = ".$gmailAccount->email);
            }
             
        }
        
        $this->logInfo('Add Analytics Job Finished');
    }
}
