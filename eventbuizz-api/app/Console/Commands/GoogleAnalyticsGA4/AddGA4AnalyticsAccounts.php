<?php

namespace App\Console\Commands\GoogleAnalyticsGA4;

use Illuminate\Console\Command;
use App\Mail\Email;
use Google\Analytics\Admin\V1alpha\AnalyticsAdminServiceClient;
use Google\ApiCore\ApiException;

class AddGA4AnalyticsAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:gA4AnalyticsAccounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add GA4 Analytics Accounts and to db';

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
}
