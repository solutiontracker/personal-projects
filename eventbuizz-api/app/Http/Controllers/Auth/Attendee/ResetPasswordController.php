<?php

namespace App\Http\Controllers\Auth\Attendee;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Http\Controllers\Auth\Attendee\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    public $successStatus = 200;

    use ResetsPasswords;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest:attendee')->except('logout');
    }

    /**
     * @param ResetPasswordRequest $request
     * 
     * @return [type]
     */
    public function reset(ResetPasswordRequest $request)
    {
        // Will return only validated data
        $request->validated();

        $event = $request->event;

        $label = $event['labels'];
        
        //Merge organizer id into request object
        $request->merge([
            "organizer_id" => $event['organizer_id'],
        ]);

        $attendee = \App\Models\Attendee::where($this->credentials($request))->first();

        if($attendee) {
            $this->resetPassword($attendee, $request->password);
            return response()->json([
                'success' => true,
                'redirect' => "login"
            ], $this->successStatus);
        } else {

            return $this->sendFailedLoginResponse($request, $label['GENERAL_EMAIL_NOT_EXIST']);
        }
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
     * @param mixed $user
     * @param mixed $password
     * 
     * @return [type]
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => Str::random(60),
        ])->save();
    }

    /**
     * @param ResetPasswordRequest $request
     * 
     * @return [type]
     */
    protected function credentials(ResetPasswordRequest $request)
    {
        return array(
            'organizer_id' => $request->organizer_id,
            'email' => $request->email
        );
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
}
