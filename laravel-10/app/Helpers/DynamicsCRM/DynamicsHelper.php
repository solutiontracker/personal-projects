<?php


    namespace App\Helpers\DynamicsCRM;


    use App\Eventbuizz\Repositories\AttendeeRepository;
    use App\Models\AddAttendeeLog;
    use App\Models\DynamicsToken;
    use App\Models\Integration;
    use App\Models\IntegrationRule;
    use Carbon\Carbon;
    use GuzzleHttp\Exception\RequestException;
    use GuzzleHttp\Client;

    class DynamicsHelper
    {
        const ALIAS = "dynamics";
        public $current_token = null;

        /**
         * @param string $resource_url
         * @return string
         *
         * Create authentication route where user needs to give credentials
         */
        public static function getAuthenticationUrl(string $resource_url)
        {
            $queryParams = [
                'client_id' => env('DYNAMICS_CLIENT_ID'),
                'response_type' => 'code',
                'client_secret' => env('DYNAMICS_CLIENT_SECRET'),
                'redirect_uri' => env('DYNAMICS_REDIRECT_URL'),
                'scope' => "openid offline_access $resource_url/user_impersonation",
            ];

            $queryParams = http_build_query($queryParams);
            $url = env('DYNAMICS_AUTH_URL') . '?' . $queryParams;
            return $url;
        }

        /**
         * @param string $code
         * @return false|mixed
         * @throws \GuzzleHttp\Exception\GuzzleException
         *
         * Get Access Token from Authrization Code. Defualt expiry of Access Token is 1-hour
         */
        public static function getTokenFromCode(string $code)
        {
            $client = new Client();

            try {
                $response = $client->request('POST', env('DYNAMICS_TOKEN_URL'), [
                    'form_params' => [
                        'client_id' => env('DYNAMICS_CLIENT_ID'),
                        'redirect_uri' => env('DYNAMICS_REDIRECT_URL'),
                        'grant_type' => 'authorization_code',
                        'client_secret' => env('DYNAMICS_CLIENT_SECRET'),
                        'code' => $code,
                    ]
                ]);
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    dump($e->getResponse()->getBody()->getContents());
                    dump($e->getResponse()->getStatusCode());
                }
                return false;
            }
            $response = $response->getBody()->getContents();
            return json_decode($response);

        }

        /**
         * @param $organizer_id
         * @return bool
         * @throws \GuzzleHttp\Exception\GuzzleException
         */
        public function getTokenFromRefreshToken($organizer_id){
            $token = DynamicsToken::where('organizer_id', $organizer_id)->first();
            $new_token = DynamicsHelper::refreshToken($token->refresh_token);

            if($new_token === false){
                $token->authorized = 0;
                $token->save();
                return false;
            }

            $token->access_token = $new_token->access_token;
            $token->refresh_token = $new_token->refresh_token;
            $token->id_token = $new_token->id_token;
            $token->authorized = 1;
            $token->expires_at = Carbon::now()->addSeconds($new_token->expires_in);
            $token->save();
            return $token;
        }

        /**
         * @param string $refresh_token
         * @return false|string
         * @throws \GuzzleHttp\Exception\GuzzleException
         *
         * Get new Access Token From Refreh Token. Default Expiry oF refresh Token is 24-hours
         */
        public static function refreshToken(string $refresh_token)
        {
            $client  = new Client();

            try {
                $response = $client->request('POST', env('DYNAMICS_TOKEN_URL'), [
                    'form_params' => [
                        'client_id' => env('DYNAMICS_CLIENT_ID'),
                        'redirect_uri' => env('DYNAMICS_REDIRECT_URL'),
                        'grant_type' => 'refresh_token',
                        'client_secret' => env('DYNAMICS_CLIENT_SECRET'),
                        'refresh_token' => $refresh_token
                    ]
                ]);
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    dump($e->getResponse()->getBody()->getContents());
                    dump($e->getResponse()->getStatusCode());
                }
                return false;
            }

            return json_decode($response->getBody()->getContents());

        }

        public function syncAttendee(){
            $tokens = DynamicsToken::where('authorized', 1)->get();

            foreach($tokens as $token){

                if($token->expires_at <= Carbon::now()->subMinutes(5)){
                    $token = $this->getTokenFromRefreshToken($token->organizer_id);
                    if($token === false){
                        continue;
                    }
                }

                $this->current_token = $token;

                $integration = Integration::where('alias', DynamicsHelper::ALIAS)->first();
                $rules = IntegrationRule::where('organizer_id', $this->current_token->organizer_id)->where('integration_id', $integration->id)->get()->toArray();
                $rules = IntegrationRule::format($rules);

                $this->syncOrganizerAttendee($rules);
            }
        }


        public function syncOrganizerAttendee($rules = [])
        {
            $attendees = AttendeeRepository::getAttendeeLog($this->current_token->organizer_id);
            foreach ($attendees as $attendee) {
                if (isset($attendee['company_name']) && !empty($attendee['company_name'])) {
                    $account_id = $this->syncAccount($attendee, $rules);
                    $attendee['accountid'] = $account_id;
                }
                if($rules['create_new_contact']) {
                    $this->syncContact($attendee, $rules);
                }
                else{
                    $this->syncLead($attendee, $rules);
                }
            }
        }

        public function syncContact($attendee, $rules)
        {
            $dm_obj = new DynamicsContactHelper($this->current_token, $rules);
            $success = $dm_obj->upsert($attendee);
            $attendeeLog = AddAttendeeLog::find($attendee['log_id']);

            if ($success !== false) {
                $attendeeLog->status = 1;
            } else{
                $attendeeLog->status = 2;
            }

            $attendeeLog->save();
        }

        public function syncLead($attendee, $rules)
        {
            $dm_obj = new DynamicsLeadHelper($this->current_token, $rules);
            $success = $dm_obj->upsert($attendee);
            $attendeeLog = AddAttendeeLog::find($attendee['log_id']);

            if ($success !== false) {
                $attendeeLog->status = 1;
            } else{
                $attendeeLog->status = 2;
            }

            $attendeeLog->save();
        }

        public function syncAccount($attendee, $rules){

            $dm_obj = new DynamicsAccountHelper($this->current_token, $rules);
            if(isset($attendee['company_name']) && !empty($attendee['company_name'])){
                $id =  $dm_obj->findOrCreate($attendee['company_name']);

                return $id;
            }

            return false;
        }

    }