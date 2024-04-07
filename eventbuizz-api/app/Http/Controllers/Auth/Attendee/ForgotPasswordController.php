<?php

namespace App\Http\Controllers\Auth\Attendee;

use App\Http\Controllers\Auth\Attendee\Requests\ForgotPasswordRequest;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
     */

    public $successStatus = 200;

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
     * @param ForgotPasswordRequest $request
     *
     * @return [type]
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $event = $request->event;
        $attendee_setting = getEventAttendeeSetting($event['id']);
        $label = $event['labels'];

        // Will return only validated data
        $request->validated();

        //Merge organizer id into request object
        $request->merge([
            "organizer_id" => $event['organizer_id'],
        ]);

        //validate domain login
        $this->validateEmailDomain($attendee_setting, $request->email, $label);

        $attendee = \App\Models\Attendee::where($this->credentials($request, $attendee_setting))->first();

        if ($attendee) {
            $event_attendee = \App\Models\EventAttendee::where('event_id', $event['id'])->where('attendee_id', $attendee->id)->where('is_active', '1')->first();
            if ($event_attendee) {
                //if ($attendee_setting->authentication) {
                if ($attendee->status == '1') {
                    $authentication = \App\Models\AttendeeAuthentication::where('event_id', $event['id'])->where('email', $attendee->email)->where('refrer', 'forgot-password')->first();
                    if ($attendee->phone) {
                        if (!$authentication) {
                            //Authentication required
                            $authentication = \App\Models\AttendeeAuthentication::create([
                                'event_id' => $event['id'],
                                'email' => $attendee->email,
                                'refrer' => "forgot-password",
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
                                'refrer' => "forgot-password",
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
                            'data' => array(
                                'message' => $label['EVENTSITE_AUTHENTICATION_EMAIL_CODE_SEND_MSG'],
                                'authentication_id' => $authentication->id,
                            ),
                        ], $this->successStatus);
                    }
                } else {
                    return $this->sendFailedLoginResponse($request, $label['GENERAL_ACCOUNT_INACTIVE']);
                }
                //}
            } else {
                return $this->sendFailedLoginResponse($request, $label['EVENTSITE_ATTENDEE_NOT_ALLOWED']);
            }
        } else {
            return $this->sendFailedLoginResponse($request, $label['GENERAL_EMAIL_NOT_EXIST']);
        }
    }

    //defining which guard to use in our case, it's the attendee guard
    /**
     * @return [type]
     */
    protected function guard()
    {
        return Auth::guard('attendee');
    }

    //defining our password broker function
    /**
     * @return [type]
     */
    protected function broker()
    {
        return Password::broker('attendee');
    }

    /**
     * @param mixed $attendee_setting
     * @param mixed $email
     * @param mixed $label
     *
     * @return [type]
     */
    protected function validateEmailDomain($attendee_setting, $email, $label)
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
                return response()->json([
                    'success' => false,
                    'message' => $label['REGISTER_VALID_DOMAIN'],
                ], $this->successStatus);
            }
        }

        return true;

    }

    /**
     * @param Request $request
     * @param mixed $attendee_setting
     *
     * @return [type]
     */
    protected function credentials(Request $request, $attendee_setting)
    {
        return $request->only('email', 'organizer_id');
    }
}
