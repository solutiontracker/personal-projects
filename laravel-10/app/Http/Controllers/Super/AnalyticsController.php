<?php

namespace App\Http\Controllers\Super;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
class AnalyticsController extends Controller
{
    public $successStatus = 200;
    
    /**
     * oAuth2Callback
     *
     * @param  mixed $request
     * @return void
     */
    public function oAuth2Callback(Request $request)
    {
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

        $service = new \Google_Service_Oauth2($client);
        // Handle authorization flow from the server.
        if (!isset($request['code'])) {
            $auth_url = $client->createAuthUrl();
            return Redirect::to($auth_url);
        } else {         
            $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $refreshToken = $client->getRefreshToken();
            $user = $service->userinfo->get();
            $gaAccountExit = \App\Models\GoogleAnalyticsGmailAccount::where('email', $user->email)->first();

            if ($gaAccountExit) {
                \App\Models\GoogleAnalyticsGmailAccount::where('email', $user->email)->update([
                    "refresh_token" => $refreshToken,
                ]);
            } else {
                \App\Models\GoogleAnalyticsGmailAccount::create([
                    "email" => $user->email,
                    "refresh_token" => $refreshToken
                ]);
            }
            return Redirect::to(config("app.eventcenter_url").'/_super/analytics_requests/ga_gmail_accounts');
        }
    }
}
