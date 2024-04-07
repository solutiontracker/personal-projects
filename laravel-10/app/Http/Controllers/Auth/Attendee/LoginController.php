<?php

namespace App\Http\Controllers\Auth\Attendee;

use App\Events\Mobile\Event;
use App\Http\Controllers\Auth\Attendee\Requests\CprVerificationRequest;
use App\Http\Controllers\Auth\Attendee\Requests\LoginRequest;
use App\Http\Controllers\Auth\Attendee\Requests\VerificationRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Nodes\NemId\Login\CertificationCheck\CertificationCheck;
use App\Eventbuizz\Repositories\OrganizerRepository;

class LoginController extends Controller
{
    public $successStatus = 200;

    public function __construct()
    {
        $this->middleware('guest:attendee')->except('logout');
    }

    /**
     * @param Request $request
     * @param mixed $message
     *
     * @return [type]
     */
    protected function sendFailedLoginResponse(Request $request, $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $this->successStatus);
    }

    /**
     * @param mixed $attendee_setting
     * @param mixed $email
     *
     * @return [type]
     */
    protected function validateEmailDomain($attendee_setting, $email)
    {
        $domains = array();
        
        if (trim($attendee_setting->domain_names)) {
            $domain_names = explode(',', $attendee_setting->domain_names);
            foreach ($domain_names as $domain) {
                $domains[trim(strtolower($domain))] = trim(strtolower($domain));
            }
        }

        if (count($domains) > 0) {
            $domain_data = explode("@", $email);
            if (!in_array(strtolower($domain_data[1]), $domains)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Request $request
     * @param mixed $attendee_setting
     * @param mixed $attendee
     *
     * @return [type]
     */
    protected function validatePassword(Request $request, $attendee_setting, $attendee)
    {
        if (!$attendee_setting->authentication && !$attendee_setting->registration_password && !$attendee_setting->hide_password) {
            if (Auth::guard('attendee-web')->attempt(['email' => $attendee->email, 'organizer_id' => $request->organizer_id, 'password' => $request->password], $request->get('remember'))) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * @param LoginRequest $request
     *
     * @return [type]
     */
    public function login(LoginRequest $request)
    {
        $event = $request->event;

        $attendee_setting = getEventAttendeeSetting($event['id']);

        $organizer_id = $event['organizer_id'];

        $organizer = OrganizerRepository::getOrganizerById($organizer_id);

        $label = $event['labels'];
        
        $settings = $event['settings'];

        // Will return only validated data
        $request->validated();

        //Merge organizer id into request object
        $request->merge([
            "organizer_id" => $event['organizer_id'],
        ]);

        //validate domain login
        $domain = $this->validateEmailDomain($attendee_setting, $request->email);

        if ($domain) {

            $attendee = \App\Models\Attendee::where($this->credentials($request, $attendee_setting))->first();

            if ($attendee) {
                //validate password
                $password = $this->validatePassword($request, $attendee_setting, $attendee);
                if ($password) {
                    $event_attendee = \App\Models\EventAttendee::where('event_id', $event['id'])->where('attendee_id', $attendee->id)->where('is_active', '1')->first();
                    if ($event_attendee) {
                        if ($attendee_setting->authentication) {
                            if ($attendee->status == '1') {
                                $authentication = \App\Models\AttendeeAuthentication::where('event_id', $event['id'])->where('email', $attendee->email)->whereNull('refrer')->first();
                                if ($attendee->phone && $event['is_enable_sms'] == 1 && $organizer->enable_sms == 1) {
                                    if (!$authentication) {
                                        //Authentication required
                                        $authentication = \App\Models\AttendeeAuthentication::create([
                                            'event_id' => $event['id'],
                                            'email' => $attendee->email,
                                        ]);
                                    }

                                    return response()->json([
                                        'success' => true,
                                        'redirect' => "choose-provider",
                                        'data' => array(
                                            'authentication_id' => $authentication->id,
                                        ),
                                    ], $this->successStatus);
                                } else {
                                    //Authentication required
                                    $token = rand(100000, 999999);
                                    if (!$authentication) {
                                        $authentication = \App\Models\AttendeeAuthentication::create([
                                            'email' => $attendee->email,
                                            'event_id' => $event['id'],
                                            'token' => $token,
                                            'expire_at' => \Carbon\Carbon::now()->addMinutes(5),
                                            'type' => 'email',
                                            'to' => $attendee->email,
                                        ]);
                                    } else {
                                        $authentication->token = $token;
                                        $authentication->expire_at = \Carbon\Carbon::now()->addMinutes(5);
                                        $authentication->type = 'email';
                                        $authentication->to = $attendee->email;
                                        $authentication->save();
                                    }

                                    //send email
                                    $attendee->notify(new \App\Notifications\Auth\Attendee\TwoFactorAuthentication([
                                        "event" => $event,
                                        "authentication" => $authentication,
                                        "attendee" => $attendee,
                                    ]));

                                    return response()->json([
                                        'success' => true,
                                        'redirect' => "verification",
                                        'message' => $label['EVENTSITE_AUTHENTICATION_EMAIL_CODE_SEND_MSG'],
                                        'data' => array(
                                            'authentication_id' => $authentication->id,
                                        ),
                                    ], $this->successStatus);

                                }
                            } else {
                                return $this->sendFailedLoginResponse($request, $label['GENERAL_ACCOUNT_INACTIVE']);
                            }
                        } else {
                            if ($attendee->status == '1') {
                                //logged in
                                return $this->makeLogin($request, $attendee);
                            } else {
                                return $this->sendFailedLoginResponse($request, $label['GENERAL_ACCOUNT_INACTIVE']);
                            }
                        }
                    } else {
                        if ($attendee_setting->registration_password) {
                            $attendee = $this->makeGuestLogin($request, $attendee_setting);
                            if ($attendee) {
                                //logged in
                                return $this->makeLogin($request, $attendee);
                            } else {
                                return $this->sendFailedLoginResponse($request, $label['EVENTSITE_ATTENDEE_NOT_ALLOWED']);
                            }
                        } else {
                            return $this->sendFailedLoginResponse($request, $label['EVENTSITE_ATTENDEE_NOT_ALLOWED']);
                        }
                    }
                } else {
                    return $this->sendFailedLoginResponse($request, $label['GENERAL_EMAIL_NOT_EXIST']);
                }
            } else {
                if ($attendee_setting->registration_password) {
                    $attendee = $this->makeGuestLogin($request, $attendee_setting);
                    if ($attendee) {
                        //logged in
                        return $this->makeLogin($request, $attendee);
                    } else {
                        return $this->sendFailedLoginResponse($request, $label['EVENTSITE_ATTENDEE_NOT_ALLOWED']);
                    }
                } else {
                    return $this->sendFailedLoginResponse($request, $label['GENERAL_EMAIL_NOT_EXIST']);
                }
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => $label['REGISTER_VALID_DOMAIN'],
            ], $this->successStatus);
        }
    }

    /**
     * @param VerificationRequest $request
     *
     * @return [type]
     */
    public function verification(VerificationRequest $request, $url, $id)
    {
        $event = $request->event;
        $attendee_setting = getEventAttendeeSetting($event['id']);
        $label = $event['labels'];
        $authentication = \App\Models\AttendeeAuthentication::where('id', $id)->where('event_id', $event['id'])->first();
        if ($authentication) {
            $request->merge(["email" => $authentication->email]);
            $attendee = \App\Models\Attendee::where($this->credentials($request, $attendee_setting))->first();
            if ($request->isMethod('POST')) {
                if ($request->screen == "choose-provider") {
                    //Token add
                    $token = rand(100000, 999999);
                    $authentication->token = $token;
                    $authentication->expire_at = \Carbon\Carbon::now()->addMinutes(5);
                    $authentication->type = $request->provider;
                    $authentication->save();

                    return $this->sendByprovider($request, $attendee, $authentication, $label, $event);

                } else if ($request->screen == "verification") {
                    if ($authentication->refrer == "forgot-password") {
                        if ($attendee->status == '1') {
                            //Destroy Authentication
                            $authentication->delete();

                            return response()->json([
                                'success' => true,
                                'redirect' => "reset-password",
                            ], $this->successStatus);
                        } else {
                            return $this->sendFailedLoginResponse($request, $label['GENERAL_ACCOUNT_INACTIVE']);
                        }
                    } else {
                        if ($attendee->status == '1') {
                            //Destroy Authentication
                            $authentication->delete();
                            //logged in
                            return $this->makeLogin($request, $attendee);
                        } else {
                            return $this->sendFailedLoginResponse($request, $label['GENERAL_ACCOUNT_INACTIVE']);
                        }
                    }
                } else if ($request->screen == "resend") {
                    //update token
                    $token = rand(100000, 999999);
                    $authentication->token = $token;
                    $authentication->expire_at = \Carbon\Carbon::now()->addMinutes(5);
                    $authentication->save();

                    return $this->sendByprovider($request, $attendee, $authentication, $label, $event);
                }
            } else {
                if ($request->screen == "verification") {
                    $start = \Carbon\Carbon::now();
                    $end = new \Carbon\Carbon($authentication->expire_at);
                    $seconds = $start->diffInSeconds($end);
                    if ($start->lessThan($end) && $authentication) {
                        $seconds = ($seconds > 0 ? $seconds * 1000 : 0);
                    } else {
                        $seconds = 0;
                    }
                    return response()->json([
                        'success' => true,
                        'data' => array(
                            'ms' => $seconds,
                            'authentication_id' => $authentication->id,
                        ),
                    ], $this->successStatus);
                } else {
                    return response()->json([
                        'success' => true,
                        'data' => array(
                            'email' => maskEmail($attendee->email),
                            'phone' => maskPhoneNumber($attendee->phone),
                            'authentication_id' => $authentication->id,
                        ),
                    ], $this->successStatus);
                }
            }
        } else {
            return response()->json([
                'success' => true,
                'redirect' => "login",
            ], 203);
        }
    }

    /**
     * @param Request $request
     * @param mixed $attendee
     * @param mixed $authentication
     * @param mixed $label
     * @param mixed $event
     *
     * @return [type]
     */
    protected function sendByprovider(Request $request, $attendee, $authentication, $label, $event)
    {
        $start = \Carbon\Carbon::now();
        $end = new \Carbon\Carbon($authentication->expire_at);
        $seconds = $start->diffInSeconds($end);
        if ($start->lessThan($end) && $authentication) {
            $seconds = ($seconds > 0 ? $seconds * 1000 : 0);
        } else {
            $seconds = 0;
        }

        if ($authentication->type == "email") {
            $authentication->to = $attendee->email;
            $authentication->save();

            //send email
            $attendee->notify(new \App\Notifications\Auth\Attendee\TwoFactorAuthentication([
                "event" => $event,
                "authentication" => $authentication,
                "attendee" => $attendee,
            ]));

            return response()->json([
                'success' => true,
                'redirect' => "verification",
                'message' => $label['EVENTSITE_AUTHENTICATION_EMAIL_CODE_SEND_MSG'],
                'data' => array(
                    'authentication_id' => $authentication->id,
                    'ms' => $seconds,
                ),
            ], $this->successStatus);
        } else if ($authentication->type == "sms") {
            $authentication->to = $attendee->phone;
            $authentication->save();

            $template = getTemplate('sms', 'native_app_reset_password_sms', $event['id'], $event['language_id']);

            $subject = $template->info[0]['value'];
            $subject = str_replace("{event_name}", $event->name, $subject);
            $body = $label['EVENTSITE_AUTHENTICATION_CODE'] . ': {code} <br>' . $template->info[1]['value'];
            $body = stripslashes($body);
            $body = str_replace("{event_name}", $event['name'], $body);
            $body = str_replace("{code}", $authentication->token, $body);

            //sms authentication
            $status = sendSMS($body, $attendee->phone, $event['detail']['sms_organizer_name']);
            $this->smsHistory($request, $attendee, $status, $authentication, $body);

            return response()->json([
                'success' => true,
                'redirect' => "verification",
                'message' => $label['EVENTSITE_AUTHENTICATION_PHONE_CODE_SEND_MSG'],
                'data' => array(
                    'authentication_id' => $authentication->id,
                    'ms' => $seconds,
                ),
            ], $this->successStatus);
        }
    }

    /**
     * @param Request $request
     * @param mixed $attendee
     *
     * @return [type]
     */
    protected function loginHistory(Request $request, $attendee)
    {
        $data = array(
            [
                'attendee_id' => $attendee->id,
                'event_id' => $request->event['id'],
                'ip' => $request->ip(),
                'browser' => $request->header('User-Agent'),
                'user_agent' => $request->server('HTTP_USER_AGENT'),
                'platform' => 'Desktop App',
                'history_type' => 'Login',
            ],
        );

        //Dispatch event for attendee login activity
        event(Event::AttendeeLoginHistoryInstaller, $data);
    }

    /**
     * @param Request $request
     * @param mixed $attendee
     * @param mixed $status
     * @param mixed $authentication
     * @param mixed $body
     *
     * @return [type]
     */
    protected function smsHistory(Request $request, $attendee, $status, $authentication, $body)
    {
        $data = array(
            [
                'status' => $status,
                'event_id' => $request->event['id'],
                'organizer_id' => $request->event['organizer_id'],
                'attendee_id' => $attendee->id,
                'name' => $attendee->first_name . ' ' . $attendee->last_name,
                'email' => $attendee->email,
                'phone' => $authentication->to,
                'sms' => $body,
                'type' => '2FA',
            ],
        );

        //Dispatch event for attendee login activity
        event(Event::smsHistoryInstaller, $data);
    }

    /**
     * @param Request $request
     * @param mixed $attendee_setting
     *
     * @return [type]
     */
    protected function makeGuestLogin(Request $request, $attendee_setting)
    {
        $attendee = $this->createGuestLogin($request, $attendee_setting);
        return $attendee;
    }

    /**
     * @return [type]
     */
    protected function getInfoKeys()
    {
        $keys = array('delegate_number', 'table_number', 'age', 'gender', 'company_name', 'company_key', 'title', 'industry', 'about', 'phone', 'website', 'website_protocol', 'facebook', 'facebook_protocol', 'twitter', 'twitter_protocol', 'linkedin', 'linkedin_protocol', 'linkedin_profile_id', 'registration_type', 'country', 'organization', 'jobs', 'interests', 'initial', 'department', 'custom_field_id', 'network_group', 'billing_ref_attendee', 'billing_password', 'place_of_birth', 'passport_no', 'date_of_issue_passport', 'date_of_expiry_passport', 'private_house_number', 'private_street', 'private_post_code', 'private_city', 'private_country');
        return $keys;
    }

    /**
     * @param Request $request
     * @param mixed $attendee_setting
     *
     * @return [type]
     */
    protected function createGuestLogin(Request $request, $attendee_setting)
    {
        $event = $request->event;
        $event_id = $request->event['id'];
        $languages = get_event_languages($event_id);
        $fields = $this->getInfoKeys();
        $organizer_id = $event['organizer_id'];
        $first_name = 'Guest';
        $last_name = substr(time(), -5);
        $default_password = $attendee_setting->default_password;
        if ($default_password) {
            $attendee_password = $default_password;
        } else {
            $attendee_password = '123456';
        }
        $password = bcrypt($attendee_password);
        $data = array();
        $data['event_id'] = $event_id;
        $data['organizer_id'] = $organizer_id;
        $data['first_name'] = $first_name;
        $data['last_name'] = $last_name;
        $data['email'] = $request->email;
        $data['password'] = $password;
        $data['image'] = '';
        $data['status'] = '1';
        $attendee = \App\Models\Attendee::where('email', $request->email)->where('organizer_id', $organizer_id)->first();
        if (!$attendee) {
            $attendee = \App\Models\Attendee::create($data);
            //Info data
            foreach ($languages as $language_id) {
                foreach ($fields as $field) {
                    if ($field == 'custom_field_id') {
                        $field = $field . $event_id;
                    }
                    $info['attendee_id'] = $attendee->id;
                    $info['languages_id'] = $language_id;
                    $info['name'] = $field;
                    $info['value'] = '';
                    $info['status'] = '1';
                    \App\Models\AttendeeInfo::create($info);
                }
            }
        } else {
            $attendee_id = $attendee->id;
            foreach ($languages as $language_id) {
                foreach ($fields as $field) {
                    if ($field == 'custom_field_id') {
                        $field = $field . $event_id;
                    }
                    $info = \App\Models\AttendeeInfo::where('attendee_id', $attendee_id)->where('languages_id', $language_id)->where('name', $field)->first();
                    if (!$info) {
                        $info = \App\Models\AttendeeInfo::where('attendee_id', $attendee_id)->where('name', $field)->first();
                        $value = $info->value;
                        \App\Models\AttendeeInfo::create([
                            "name" => $field,
                            "attendee_id" => $attendee_id,
                            "languages_id" => $language_id,
                            "value" => is_null($value) ? '' : $value,
                            "status" => 1,
                        ]);
                    }
                }

            }
        }

        //Event attendee
        $event_attendee_data = array();
        $event_attendee_data['email_sent'] = 0;
        $event_attendee_data['sms_sent'] = 0;
        $event_attendee_data['login_yet'] = 0;
        $event_attendee_data['status'] = 1;
        $event_attendee_data['attendee_id'] = $attendee->id;
        $event_attendee_data['event_id'] = $event_id;
        $event_attendee_data['speaker'] = 0;
        $event_attendee_data['sponser'] = 0;
        $event_attendee_data['exhibitor'] = 0;
        $event_attendee_data['default_language_id'] = $event['language_id'];
        \App\Models\EventAttendee::create($event_attendee_data);

        return $attendee;
    }

    /**
     * @param Request $request
     * @param mixed $attendee
     *
     * @return [type]
     */
    protected function makeLogin(Request $request, $attendee)
    {
        $tokenResult = $attendee->createToken("virtual_app");
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = \Carbon\Carbon::now()->addWeeks(1);
        }

        $token->save();

        //Dispatch event for attendee activity
        $event_id = $request->event['id'];

        $data = array(
            [
                'user_id' => $attendee->id,
                'event_id' => $event_id,
                'ip' => $request->ip(),
                'browser' => $request->header('User-Agent'),
                'os' => $request->server('HTTP_USER_AGENT'),
                'platform' => 'Desktop App',
                'history_type' => 'Login',
            ],
        );

        event(Event::AttendeeActivityInstaller, $data);

        //login history
        $this->loginHistory($request, $attendee);

        return response()->json([
            'success' => true,
            'redirect' => "dashboard",
            'data' => array(
                'access_token' => $tokenResult->accessToken,
                'autologin_token' => $token->id,
                'token_type' => 'Bearer',
                'expires_at' => \Carbon\Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString(),
                'user' => array(
                    'id' => $attendee->id,
                    'name' => $attendee->first_name . ' ' . $attendee->last_name,
                    'first_name' => $attendee->first_name,
                    'last_name' => $attendee->last_name,
                    'email' => $attendee->email,
                    'image' => $attendee->image,
                ),
            ),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param mixed $attendee_setting
     *
     * @return [type]
     */
    protected function credentials(Request $request, $attendee_setting)
    {
        return array(
            'organizer_id' => $request->organizer_id,
            'email' => $request->email,
        );
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    //defining which guard to use in our case, it's the organizer guard
    /**
     * @return [type]
     */
    protected function guard()
    {
        return Auth::guard('organizer');
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function cprLogin(Request $request)
    {
        $event = $request->event;
        $label = $event['labels'];
        $config = config('nemid');
        if ($request->isMethod('PUT')) {
            $response = base64_decode($request->response);
            $doc = @simplexml_load_string($response);
            if (!$doc) {
                return response()->json([
                    "success" => true,
                    "redirect" => "login",
                ], $this->successStatus);
            }
            
            $userCertificate = new CertificationCheck($config);
            $certificate = $userCertificate->checkAndReturnCertificate($response);
            $pid = $certificate->getSubject()->getPid();
            $attendee = \App\Models\Attendee::where('pid', $pid)->where('organizer_id', $request->event['organizer_id'])->first();
            if ($attendee) {
                $event_attendee = \App\Models\EventAttendee::where('event_id', $event['id'])->where('attendee_id', $attendee->id)->where('is_active', '1')->first();
                if ($event_attendee) {
                    if ($attendee->status == '1') {
                        //logged in
                        return $this->makeLogin($request, $attendee);
                    } else {
                        return response()->json([
                            "success" => true,
                            "message" => $label['GENERAL_ACCOUNT_INACTIVE'],
                            "redirect" => "login",
                        ], $this->successStatus);
                    }
                } else {
                    return response()->json([
                        "success" => true,
                        "message" => $label['GENERAL_EVENT_NOT_ASSIGNED'],
                        "redirect" => "login",
                    ], $this->successStatus);
                }
            } else {
                return response()->json([
                    "success" => true,
                    "redirect" => "cpr-verification",
                    "data" => array(
                        "authentication_id" => $pid,
                    ),
                ], $this->successStatus);
            }
        } else {
            $login = new \Nodes\NemId\Login\Login($config);
            $parameters = [];
            foreach (json_decode($login->getParams(), true) as $param => $value) {
                $parameters[] = sprintf('"%s":"%s"', $param, $value);
            }

            $parameters = implode(',', $parameters);

            return response()->json([
                'success' => true,
                'data' => array(
                    'parameters' => $parameters,
                    'baseUrl' => $login->getBaseUrl(),
                    'iframeUrl' => $login->getIFrameUrl(),
                ),
            ], $this->successStatus);
        }
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function cprVerification(CprVerificationRequest $request)
    {
        $event = $request->event;
        $label = $event['labels'];
        $config = config('nemid');
        $pidCprMatch = new \Nodes\NemId\Webservice\PidCprMatch\PidCprMatch($config);
        $response = $pidCprMatch->pidCprRequest($request->pid, $request->cpr);
        if ($response->getCode() == -1 || $response->getCode() == 1) {
            return response()->json([
                "success" => true,
                "message" => $label['GENERAL_SOMETHING_WENT_WRONG'],
                "redirect" => "cpr-login",
            ], $this->successStatus);
        }
        $attendee = \App\Models\Attendee::where('ss_number', md5($request->cpr))->where('organizer_id', $request->event['organizer_id'])->first();

        if ($attendee) {
            \App\Models\Attendee::where('id', $attendee->id)->where('organizer_id', $request->event['organizer_id'])->update(['pid' => $request->pid, 'pid_date' => date('Y-m-d H:i:s')]);
            $event_attendee = \App\Models\EventAttendee::where('event_id', $event['id'])->where('attendee_id', $attendee->id)->where('is_active', '1')->first();
            if ($event_attendee) {
                if ($attendee->status == '1') {
                    //logged in
                    return $this->makeLogin($request, $attendee);
                } else {
                    return $this->sendFailedLoginResponse($request, $label['GENERAL_ACCOUNT_INACTIVE']);
                }
            } else {
                return $this->sendFailedLoginResponse($request, $label['GENERAL_EVENT_NOT_ASSIGNED']);
            }
        } else {
            return response()->json([
                "success" => true,
                "message" => $label['GENERAL_EVENT_NOT_ASSIGNED'],
                "redirect" => "cpr-login",
            ], $this->successStatus);
        }
    }
}
