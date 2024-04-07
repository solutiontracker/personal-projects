<?php


    namespace App\Helpers\SalesForce;


    use App\Eventbuizz\Repositories\AttendeeRepository;
    use App\Models\AddAttendeeLog;
    use App\Models\Integration;
    use App\Models\IntegrationRule;
    use App\Models\Organizer;
    use App\Models\SalesforceToken;
    use Carbon\Carbon;
    use Frankkessler\Guzzle\Oauth2\GrantType\RefreshToken;
    use Frankkessler\Guzzle\Oauth2\Oauth2Client;
    use Frankkessler\Salesforce\Repositories\TokenRepository;
    use Frankkessler\Salesforce\Salesforce;
    use Frankkessler\Salesforce\SalesforceConfig;
    use GuzzleHttp\Client;

    class SalesForceHelper
    {
        const ALIAS = "salesforce";


        public function syncAttendees()
        {
            $tokens = SalesforceToken::all();
            foreach ($tokens as $token) {
                $this->syncOrganizerAttendee($token);
            }
        }

        public function getObjectDescription($object)
        {
            $salesforce = new Salesforce();
            $custom     = new SalesforceCustom($salesforce);
            return $custom->get("sobjects/$object/describe/");
        }

        public function checkToken(SalesforceToken $token)
        {
            $date = Carbon::now('UTC')->toDateTimeString();

            if ($token->expires > $date) return true;

            try {

                \EBForrest::refresh();
                \EBForrest::saveUserToken($token->user_id);
            } catch (\Exception $e) {
                dump("Cannot refresh Token ({$token->user_id}).", $e->getMessage());
                return false;
            }

            return true;
        }

        public function refreshToken($organizer_id)
        {

            $repository = new TokenRepository();

            $base_uri = 'https://login.salesforce.com/services/oauth2/token';

            $refresh_token = SalesforceToken::where('user_id', $organizer_id)->first()->refresh_token;
            $http          = new Client();
            $response      = $http->post($base_uri, [
                'form_params' => [
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => $refresh_token,
                    'client_id'     => SalesforceConfig::get('salesforce.oauth.consumer_token'),
                    'client_secret' => SalesforceConfig::get('salesforce.oauth.SALESFORCE_OAUTH_CONSUMER_SECRET'),
                ],
            ]);

            dump($response->getBody()->getContents());
        }

        public function syncOrganizerAttendee($token)
        {
            $organizer_id = $token->user_id;

            \EBForrest::setUpInstance(json_decode($token->token_body, true));

            if (!$this->checkToken($token)) {
                return;
            }

            $integration = Integration::where('alias', SalesForceHelper::ALIAS)->first();
            $rules       = IntegrationRule::where('organizer_id', $organizer_id)->where('integration_id', $integration->id)->get()->toArray();
            $rules       = IntegrationRule::format($rules);
            $attendees   = AttendeeRepository::getAttendeeLog($organizer_id);


            foreach ($attendees as $attendee) {

                if (!isset($attendee['company_name']) || empty($attendee['company_name'])) {
                    $this->syncContact($attendee, $rules);
                } else {
                    $this->syncAccount($attendee, $rules);
                    if ($rules['create_new_contact']) {
                        $this->syncContact($attendee, $rules);
                    } else {
                        $this->syncLead($attendee, $rules);
                    }
                }
            }
        }

        public function syncContact($attendee, $rules)
        {

            $sf_obj      = new SalesForceContactHelper($rules);
            $success     = $sf_obj->upsert($attendee);
            $attendeeLog = AddAttendeeLog::find($attendee['log_id']);

            if ($success !== false) {
                $attendeeLog->status = 1;
            } else {
                $attendeeLog->status = 2;
            }

            $attendeeLog->save();
        }

        public function syncAccount($attendee, $rules)
        {

            $sf_obj = new SalesForceAccountHelper($rules);
            if (isset($attendee['company_name']) && !empty($attendee['company_name'])) {
                $id = $sf_obj->findOrCreate($attendee['company_name']);
            }
        }

        public function syncLead($attendee, $rules)
        {

            $sf_obj  = new SalesForceLeadHelper($rules);
            $success = $sf_obj->upsert($attendee);

            $attendeeLog = AddAttendeeLog::find($attendee['log_id']);

            if ($success !== false) {
                $attendeeLog->status = 1;
            } else {
                $attendeeLog->status = 2;
            }

            $attendeeLog->save();
        }
    }