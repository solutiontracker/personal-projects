<?php

namespace App\Http\Controllers\Auth\Lead;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\Lead\Requests\LoginRequest;
use App\Http\Controllers\Auth\Lead\Requests\VerificationRequest;
use App\Http\Controllers\Auth\Lead\Requests\SendVerificationCodeRequest;
use App\Http\Controllers\Auth\Lead\Requests\ForgotPasswordRequest;
use App\Http\Controllers\Auth\Lead\Requests\ResetPasswordRequest;
use Composer\XdebugHandler\Status;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public $successStatus = 200;

    public function __construct()
    {

    }
        
    /**
     * login
     *
     * @param  mixed $request
     * @return void
     */
    public function login(LoginRequest $request)
    {
      if ($request['login_with_auth_code']) {
        return  $this->loginWithAuthCode($request);
      }
      return  $this->loginWithPassword($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'success' => true,
        ]);
    }
        
    /**
     * loginWithAuthCode
     *
     * @param  mixed $request
     * @return void
     */
    public function loginWithAuthCode($request)
    {
      if($request['mode'] === 'contact_person'){
        return   $this->loginContactPersonWithAuthCode($request);
      }
      return $this->loginLeadUserWithAuthCode($request);
    }
    
    /**
     * loginWithPassword
     *
     * @param  mixed $request
     * @return void
     */
    public function loginWithPassword($request)
    {
      if($request['mode'] === 'contact_person'){
        return   $this->loginContactPersonWithPassword($request);
      }
      return $this->loginLeadUserWithPassword($request);
    }
    
    /**
     * loginContactPersonWithAuthCode
     *
     * @param  mixed $request
     * @return void
     */
    public function loginContactPersonWithAuthCode($request)
    {   
      // validate Organizer
        $event = $request["event"];
          
        $validateOrganizer = $this->validateOrganizer($event['organizer_id']);
        if(!$validateOrganizer['status']){
          return response()->json($validateOrganizer, $this->successStatus);
        }
        // validate contactPerson email, approved, status, verified
        $validateContactPersonBeforeLogin = $this->validateContactPersonBeforeLogin($request['email'], $event);
        if($validateContactPersonBeforeLogin['status'] !== 1){
          return response()->json($validateContactPersonBeforeLogin, $this->successStatus);

        }     
        $contact_person = \App\Models\Attendee::where('email', $request['email'])->where('organizer_id', $event['organizer_id'])->whereNull('deleted_at')->first();
        
        $response = $this->checkUserPhone($event, $request['email'], $request['mode'], $contact_person);

        return response()->json($response, $this->successStatus);

    }
        
        
    /**
     * loginLeadUserWithAuthCode
     *
     * @param  mixed $request
     * @return void
     */
    public function loginLeadUserWithAuthCode($request)
    {   
        // validate Organizer
        $event = $request["event"];
        
        $validateOrganizer = $this->validateOrganizer($event['organizer_id']);
        if(!$validateOrganizer['status']){     
          return response()->json($validateOrganizer, $this->successStatus);
        }
        if(!$this->leadsModuleStatus($event['id'])){
            return response()->json([
              'status' => 0,
              'response_type' => INACTIVE_LEADS_MODULE,
              'message' => 'Leads module is inactive'
            ], $this->successStatus);
        }
        // validate Lead user email, approved, status, verified
        $validateLeadUserBeforeLogin = $this->validateLeadUserBeforeLogin($request['email'], $event['id'], true);
        if($validateLeadUserBeforeLogin['status'] !== 1){
          return response()->json($validateLeadUserBeforeLogin, $this->successStatus);
        }

        $lead_user = \App\Models\LeadUser::where('email', $request['email'])->where('event_id', $event['id'])->whereNull('deleted_at')->first();
        // Check phone exists
        $response = $this->checkUserPhone($event, $request['email'], $request['mode'], $lead_user);
        return response()->json($response, $this->successStatus);
    }
    
    /**
     * loginContactPersonWithPassword
     *
     * @param  mixed $request
     * @return void
     */
    public function loginContactPersonWithPassword($request)
    {
        $event = $request["event"];
        // validate contactPerson 
        $validateContactPersonBeforeLogin = $this->validateContactPersonBeforeLogin($request['email'], $event);
       
        if($validateContactPersonBeforeLogin['status'] !== 1){
          return response()->json($validateContactPersonBeforeLogin, $this->successStatus);
        } 
        // fetch contactPerson
        $contact_person = \App\Models\Attendee::with('currentEventAttendee')->where('email', $request['email'])->where('organizer_id', $event['organizer_id'])->whereNull('deleted_at')->first();
        // checkHash password
        if(!\Hash::check($request["password"], $contact_person['password'])){
            return response()->json([
              'status' => 0,
              'response_type' => INVALID_PASSWORD,
              'message' => 'Invalid email or password.'
            ], $this->successStatus);
        }
        // makeLogin
        $authUserAndToken = $this->makeLogin($request, $contact_person, $request['mode']);

        return response()->json([
          "status" => 1,
          'data' => $authUserAndToken
        ], $this->successStatus);

    }
    
    /**
     * loginLeadUserWithPassword
     *
     * @param  mixed $request
     * @return void
     */
    public function loginLeadUserWithPassword($request)
    {
      $event = $request["event"];
      // fetch leadUser

      if(!$this->leadsModuleStatus($event['id'])){
        return response()->json([
          'status' => 0,
          'response_type' => INACTIVE_LEADS_MODULE,
          'message' => 'Leads module is inactive'
        ], $this->successStatus);
      }

      $lead_user = \App\Models\LeadUser::where('email', $request['email'])->where('event_id', $event['id'])->whereNull('deleted_at')->first();
      // validate ContactPerson
      if(!$lead_user){
        return response()->json([
          'status' => 0,
          'response_type' => INVALID_LEAD_USER_EMAIL,
          'message' => 'User not found'
        ], $this->successStatus);
      }
      // validate contactPerson 
      $validateLeadUserBeforeLogin = $this->validateLeadUserBeforeLogin($request['email'], $event['id']);
      if($validateLeadUserBeforeLogin['status'] !== 1){
        return response()->json($validateLeadUserBeforeLogin, $this->successStatus);
      } 
      // checkHash password
      if(!\Hash::check($request["password"], $lead_user['password'])){
        return response()->json([
          'status' => 0, 
          'response_type' => INVALID_PASSWORD, 
          'message' => 'Invalid email or password.' 
        ], $this->successStatus);
      }
      // makeLogin
      $authUserAndToken = $this->makeLogin($request, $lead_user, $request['mode']);

      return response()->json([
      "status" => 1,
      'data' => $authUserAndToken],
       $this->successStatus);
    }
    
    /**
     * validateOrganizer
     *
     * @param  mixed $organizer_id
     * @return void
     */
    function validateOrganizer($organizer_id)
    {
        $return = [
            'status' => 1,
            'message' => 'success'
        ];

        $organizer = \App\Models\Organizer::select('id')->where('id', $organizer_id)->pluck('id')->first();
        if (is_null($organizer)) {
                $return['status'] = 0;
                $return['response_type'] = EVENT_ORGANIZER_NOT_FOUND;
                $return['message'] = 'Event Organizer not Found';
        } 

        return $return;
    }
    
        
    /**
     * validateLeadUserBeforeLogin
     *
     * @param  mixed $email
     * @param  mixed $event_id
     * @return void
     */
    public function validateLeadUserBeforeLogin($email, $event_id, $login_with_auth_code=false)
    {
        $return = [
            'status' => 1,
            'message' => 'success'
        ];

        $lead_user = \App\Models\LeadUser::where('email',$email)->where('event_id', $event_id)->whereNull('deleted_at')->first();
        $leadSettings = \App\Models\LeadSetting::where("event_id", $event_id)->first();
        if (is_null($lead_user)){
            $return['status'] = 0;
            $return['response_type'] = LEAD_USER_NOT_FOUND;
            $return['message'] = 'Please provide a valid email';
        }
        elseif ($leadSettings->enable_organizer_approval == 1 && $lead_user->approved !== 1) {
            $return['status'] = 0;
            $return['response_type'] = LEAD_USER_NOT_APPROVED;
            $return['message'] = 'You are not approved.';
        }
        else {
            if ($lead_user->status == 0) {
                $return['status'] = 0;
                $return['response_type'] = LEAD_USER_INACTIVE;
                $return['message'] = 'You account has been deactivated.';
            }
        }

        return $return;
    }

    public function validateContactPersonBeforeLogin($email, $event)
    {

        $contact_person = \App\Models\Attendee::with('currentEventAttendee')
        ->where('email', $email)
        ->where('organizer_id', $event['organizer_id'])
        ->whereNull('deleted_at')
        ->first();
        
        if (!$contact_person || !$contact_person->currentEventAttendee) {
          return [
            'status' => 0,
            'response_type' => INVALID_CONTACT_PERSON_EMAIL,
            'message' => 'Not assigned to this event.'
          ];
        }
        
        $sponsorsCount = 0;
        $exhibitorsCount = 0;
        
        $checkIfSponsorContactPerson = \App\Models\Attendee::with('attachedSponsor')
        ->where('email', $email)
        ->where('organizer_id', $event['organizer_id'])
        ->whereNull('deleted_at')
        ->first();

        
        $checkIFExhibitorContactPerson = \App\Models\Attendee::with('attachedExhibitor')
        ->where('email', $email)
        ->where('organizer_id', $event['organizer_id'])
        ->whereNull('deleted_at')
        ->first();
        
        if($checkIfSponsorContactPerson && $checkIfSponsorContactPerson->attachedSponsor)
        {
          $sponsorsCount = $checkIfSponsorContactPerson->attachedSponsor->count();
        }
        
        if($checkIFExhibitorContactPerson && $checkIFExhibitorContactPerson->attachedSponsor)
        {
          $exhibitorsCount = $checkIFExhibitorContactPerson->attachedExhibitor->count();
        }
        
        if($sponsorsCount === 0 && $exhibitorsCount === 0){        
          return [
            'status' => 0,
            'response_type' => USER_NOT_ATTACHED_TO_SPONSOR_OR_EXHIBITOR,
            'message' => 'Not attatched to sponsor/exhibitor.'
          ];
        }

        return  [
          'status' => 1,
          'message' => 'success'
        ];

    }

    public function checkUserPhone($event, $email, $mode, $user)
    {
      $response = array();
      if ($user['phone'] != null && $user['phone'] != "" && strlen($user['phone']) > 4) {
        //Authentication required
        $authentication = \App\Models\AttendeeAuthentication::create([
          'email' => $email,
          'refrer' => "twoFactor",
          'event_id' => $event['id']
        ]);

      $response = [
        "status" => 1,
        "response_type" => CHOOSE_AUTH_VERIFICATION_SEND_TO,
        "data" => [
          "message" => 'Please select an Option',
          "user_id" => $user['id'],
          "event_id" => $event['id'],
          "phone" => $user['phone'],
          "email" => $email,
          "authentication_id" => $authentication->id,
        ]
      ];

      } else {
        //Authentication required
        $token = rand(100000, 999999);
        $authentication = \App\Models\AttendeeAuthentication::create([
          'email' => $email,
          'token' => $token,
          'expire_at' => \Carbon\Carbon::now()->addMinutes(5),
          'type' => 'email',
          'to' => $email,
          'refrer' => "twoFactor",
          'event_id' => $event['id']
        ]);

        $this->sendCodeViaEmail($email, $user,  $authentication, $event, $mode);

        //Expire at
        $start = \Carbon\Carbon::now();
        $end = new \Carbon\Carbon($authentication->expire_at);
        $seconds = $start->diffInSeconds($end);
        if ($start->lessThan($end) && $authentication) {
          $seconds = ($seconds > 0 ? $seconds * 1000 : 0);
        } else {
          $seconds = 0;
        }

      $response = [
        "status" => 1,
        "response_type" => AUTH_VERIFICATION_SEND_TO_EMAIL,
        "data" => [
          "message" => 'For verification purpose, a code has been sent to your email address and should arrive in your email inbox momentally. Please check your email and, once received, enter the code.',
          "user_id" => $user['id'],
          "event_id" => $event['id'],
          "phone" => $user['phone'],
          "email" => $email,
          "authentication_id" => $authentication->id,
          "expiry_at" => $seconds
        ]
      ];
      }

      return $response;
    }

    
    /**
     * sendCodeViaEmail
     *
     * @param  mixed $email
     * @param  mixed $authentication
     * @param  mixed $event_id
     * @return void
     */
    public function sendCodeViaEmail($email, $user,  $authentication, $event, $mode)
    {
       $user->notify(new \App\Notifications\Auth\Lead\TwoFactorAuthentication([
                "event" => $event,
                "authentication" => $authentication,
                "user" => $user,
                "mode" => $mode
            ]));
       return true;
    }

    public function sendCodeViaSms($event, $authentication, $user)
    {
      $template = getTemplate('sms', 'native_app_reset_password_sms', $event['id'], $event['language_id']);

      $subject = $template->info[0]['value'];
      $subject = str_replace("{event_name}", $event->name, $subject);
      $body =  'Your leads application verification : {code} <br>' . $template->info[1]['value'];
      $body = stripslashes($body);
      $body = str_replace("{event_name}", $event['name'], $body);
      $body = str_replace("{code}", $authentication->token, $body);

      //sms authentication
      $status = sendSMS($body, $user->phone, $event['organizer_name']);
      return $status;
    }
    
    /**
     * makeLogin
     *
     * @param  mixed $request
     * @param  mixed $user
     * @param  mixed $mode
     * @return void
     */
    public function makeLogin($request, $user, $mode)
    {
      if($user->token()){
        $user->token()->revoke();
      }
      $tokenResult = $user->createToken("lead_app");
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = \Carbon\Carbon::now()->addWeeks(1);
        }
        $token->save();

        //Dispatch event for attendee activity
        $event_id = $request->event['id'];
        $name =  $mode === "lead_user" ? $user->name : $user->first_name . ' ' . $user->last_name;
        return [
          'access_token' => $tokenResult->accessToken,
          'autologin_token' => $token->id,
          'token_type' => 'Bearer',
          'expires_at' => \Carbon\Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString(),
          'user' => [
                    'id' => $user->id,
                    'name' => $name,
                    'email' => $user->email,
                    'event_id' => $event_id
                ]
        ];
    }
    
    /**
     * verifyVerificationCode
     *
     * @param  mixed $request
     * @return void
     */
    public function verifyVerificationCode(VerificationRequest $request)
    {
        // Get Authentication details
        $authenticate = $this->verifyAuthCode($request['authentication_id'], $request['auth_code']);
        if($authenticate['status'] !== 1){
          return response()->json($authenticate, $this->successStatus);
        }

        if($request['mode'] != 'lead_user')
        {
            $user = \App\Models\Attendee::where('id', $request['user_id'])->where('email', $request['email'])->first();
        }
        else
        {
          $user = \App\Models\LeadUser::where('id', $request['user_id'])->where('email', $request['email'])->first();
        }

        if(!$user)
        {
          return response()->json([
            'status' => 0, 
            'response_type' => USER_NOT_FOUND, 
            'message' => "User not found invalid email/id" 
          ], $this->successStatus);
        }

        if($request['mode'] === 'lead_user'){
            if($user->verified !== 1){
                $user->verified = 1;
                $user->save();
            }
        }

        $authUserAndToken = $this->makeLogin($request, $user, $request['mode']);
        return response()->json(["status" => 1, 'data' => $authUserAndToken], $this->successStatus);
    }
    
    /**
     * sendVerificationCodeTo
     *
     * @param  mixed $request
     * @return void
     */
    public function sendVerificationCodeTo(SendVerificationCodeRequest $request)
    {
      $authentication = \App\Models\AttendeeAuthentication::where('id', $request['authentication_id'])->first();
      if(!$authentication){
        return response()->json([
          'status' => 0, 
          'response_type' => INVALID_AUTHENTICATION_ID, 
          'message' => "Invalid Athentication id" 
        ], $this->successStatus);
      }
      $response = $this->updateAndSendVerificationCode($authentication, $request);
      return response()->json(["status" => 1,'data' =>  $response], $this->successStatus);
    }

    public function resendVerificationCode(SendVerificationCodeRequest $request)
    {
      $authentication = \App\Models\AttendeeAuthentication::where('id', $request['authentication_id'])->first();
      if(!$authentication){
        return response()->json([
          'status' => 0, 
          'response_type' => INVALID_AUTHENTICATION_ID, 
          'message' => "Invalid Athentication id" 
        ], $this->successStatus);
      }
      $response = $this->updateAndSendVerificationCode($authentication, $request);
      return response()->json(["status" => 1, 'data' =>  $response], $this->successStatus);
    }
    
    /**
     * updateAndSendVerificationCode
     *
     * @param  mixed $authentication
     * @param  mixed $request
     * @return void
     */
    public function updateAndSendVerificationCode($authentication, $request)
    {
      $token = rand(100000, 999999);
      $authentication->token = $token;
      $authentication->expire_at = \Carbon\Carbon::now()->addMinutes(5);
      $authentication->type = $request['send_to'];
      $authentication->to = $request['send_to'] === 'email' ? $request['email'] : $request['phone'];
      $authentication->save();

      if($request['mode'] != 'lead_user')
      {
          $user = \App\Models\Attendee::where('id', $request['user_id'])->where('email', $request['email'])->first();
      }
      else
      {
        $user = \App\Models\LeadUser::where('id', $request['user_id'])->where('email', $request['email'])->first();
      }
      if(!$user)
      {
        return response()->json([
          'status' => 0, 
          'response_type' => USER_NOT_FOUND, 
          'message' => "User not found invalid email/id" 
        ], $this->successStatus);
      }
       
      $response = [
        "user_id"=> $user->id,
        "event_id"=> $request['event_id'],
        "email"=> $user->email,
        "phone"=> $user->phone,
        "authentication_id"=> $authentication->id,
      ];
      
      if($request['send_to'] === 'phone'){
        $this->sendCodeViaSms($request['event'], $authentication, $user);
        $response['sent_to'] = $request['send_to'];       
      }
      else{  
        $this->sendCodeViaEmail($request['email'], $user,$authentication,$request['event'], $request['mode']);
        $response['sent_to'] = $request['send_to'];       
      }
      return $response;
    }
    
    /**
     * forgotPassword
     *
     * @param  mixed $request
     * @return void
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
     
      if($request['mode'] === "lead_user") {
          $user = \App\Models\LeadUser::where('email', $request['email'])->where('event_id', $request['event_id'])->first();
        }
      else{
        $attendee = \App\Models\Attendee::where('email', $request['email'])->where('organizer_id', $request['event']['organizer_id'])->first();
        $eventAttendee = \App\Models\EventAttendee::where('attendee_id', $attendee->id)->where('event_id', $request['event_id'])->first();
        $user = $eventAttendee ? $attendee : null;
      }
      if(!$user)
      {
        return response()->json([
          'status' => 0, 
          'response_type' => USER_NOT_FOUND, 
          'message' => "No user with this email Found" 
        ], $this->successStatus);
      }

      if(($user['phone'] != null && $user['phone'] != "" && strlen($user['phone']) > 4)){
        //Authentication required
        $authentication = \App\Models\AttendeeAuthentication::create([
          'email' => $user['email'],
          'refrer' => "twoFactor",
          'event_id' => $request['event_id']
        ]);

          $response = [
            "status" => 1,
            "response_type" => CHOOSE_AUTH_VERIFICATION_SEND_TO,
            "data" => [
              "message" => 'Please select an Option',
              "user_id" => $user['id'],
              "event_id" => $request['event_id'],
              "phone" => $user['phone'],
              "email" => $user['email'],
              "authentication_id" => $authentication->id,
            ]
          ];
      }
      else{
          $token = rand(100000, 999999);
          $authentication = \App\Models\AttendeeAuthentication::create([
            'email' => $request['email'],
            'token' => $token,
            'expire_at' => \Carbon\Carbon::now()->addMinutes(5),
            'type' => $request['send_to'],
            'to' => $request['send_to'] != "phone" ? $request['email'] : $request['phone'],
            'refrer' => "forgot-password",
            'event_id' => $request['event']['id']
          ]);
  
          $response = [
            "status" => 1,
            "response_type" => AUTH_VERIFICATION_SEND_TO_EMAIL,
            "data" => [
              "message" => "For verification purpose, a code has been sent to your email address and should arrive in your email inbox momentally. Please check your email and, once received, enter the code.",
              "user_id" => $user->id,
              "event_id"=> $request['event_id'],
              "email"=> $user->email,
              "phone"=> $user->phone,
              "authentication_id"=> $authentication->id,
            ]
          ];
          
          $this->sendCodeViaEmail($request['email'], $user,$authentication,$request['event'], $request['mode']);
          $response['sent_to'] = "email";        
      }

      return response()->json($response, $this->successStatus);
    }
    
    /**
     * verifyAuthCode
     *
     * @param  mixed $authentication_id
     * @param  mixed $authentication_code
     * @return void
     */
    public function verifyAuthCode($authentication_id, $authentication_code)
    {
      // Get Authentication details
      $authentication = \App\Models\AttendeeAuthentication::where('id', $authentication_id)->first();
      if(!$authentication){
          return [
            'status' => 0, 
            'response_type' => INVALID_AUTHENTICATION_ID, 
            'message' => "Invalid Authentication id" 
          ];
      }
      
      if($authentication->token !== $authentication_code){
          return [
            'status' => 0, 
            'response_type' => INVALID_VERIFICATION_CODE, 
            'message' => "Invalid Verification Code" 
          ];
      }

      $start = \Carbon\Carbon::now();
      $end = new \Carbon\Carbon($authentication->expire_at);
      $seconds = $start->diffInSeconds($end);
      if ($start->lessThan($end)) {
          $timer = gmdate('i:s', $seconds);
      } else {
          $timer = 0;
      }

      if($timer === gmdate('i:s', 0))
      {
        return [
          'status' => 0, 
          'response_type' => VERIFICATION_CODE_EXPIRED, 
          'message' => "Verification Code time expired" 
        ];
      }

      return ['status' => 1, 'message' => "success" ];

    }
    
    /**
     * verifyforgotPasswordCode
     *
     * @param  mixed $request
     * @return void
     */
    public function verifyforgotPasswordCode(VerificationRequest $request)
    {
        // Get Authentication details
        $authenticate = $this->verifyAuthCode($request['authentication_id'], $request['auth_code']);
        if($authenticate['status'] !== 1){
          return response()->json($authenticate, $this->successStatus);
        }
        if($request['mode'] === "lead_user") {
          $user = \App\Models\LeadUser::where('email', $request['email'])->where('event_id', $request['event_id'])->first();
        }
        else{
          $attendee = \App\Models\Attendee::where('email', $request['email'])->where('organizer_id', $request['event']['organizer_id'])->first();
          $eventAttendee = \App\Models\EventAttendee::where('attendee_id', $attendee->id)->where('event_id', $request['event_id'])->first();
          $user = $eventAttendee ? $attendee : null;
        }
        if(!$user)
        {
          return response()->json([
            'status' => 0, 
            'response_type' => INVALID_LEAD_USER_EMAIL, 
            'message' => "Invalid User email" 
          ], $this->successStatus);
        }
        if($user->id !== (int) $request['user_id']){
          return response()->json([
            'status' => 0, 
            'response_type' => INVALID_USER_ID, 
            'message' => "Invalid User ID" 
          ], $this->successStatus);
        }
    return response()->json([
        "status" => 1,
        "data" => [
          "user_id" => $user->id,
          "event_id" => $request['event']['id'],
          "email" => $user->email,
          "phone" => $user->phone,
          "verification_code" => "valid"
        ]
      ],
      $this->successStatus);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        if($request['mode'] == "lead_user") {
          $user = \App\Models\LeadUser::where('email', $request['email'])->where('event_id', $request['event_id'])->first();
        }
        else{
          $attendee = \App\Models\Attendee::where('email', $request['email'])->where('organizer_id', $request['event']['organizer_id'])->first();
          $eventAttendee = \App\Models\EventAttendee::where('attendee_id', $attendee->id)->where('event_id', $request['event_id'])->first();
          $user = $eventAttendee ? $attendee : null;
          }
      if(!$user){
        return response()->json([
          'status' => 0, 
          'response_type' => INVALID_LEAD_USER_EMAIL, 
          'message' => "Invalid User Email" 
        ], $this->successStatus);
      }
      if($user->id !== (int) $request['user_id']){
        return response()->json([
          'status' => 0, 
          'response_type' => INVALID_USER_ID, 
          'message' => "Invalid User ID" 
        ], $this->successStatus);
      }
      $user->password = \Hash::make($request->new_password);
      $user->save();

      if(!$user['deleted_at']){
        unset($user['deleted_at']);
      }

      return response()->json(['status' => 1, "data" => ["user" => $user] ], $this->successStatus);
    }

    public function leadsModuleStatus($event_id)
    {
      $status = \App\Models\EventModuleOrder::select(['status'])->where('event_id', $event_id)->where('alias', 'leadsmanagment')->first();
      return $status->status;
    }


}
