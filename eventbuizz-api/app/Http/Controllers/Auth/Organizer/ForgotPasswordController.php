<?php

namespace App\Http\Controllers\Auth\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Http\Controllers\Auth\Organizer\Requests\ForgotPasswordRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;

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
        $this->middleware('guest:organizer')->except('logout');
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        // Will return only validated data
        $request->validated();
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            ['email' => $request->email]
        );
        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($response, $request->email)
            : $this->sendResetLinkFailedResponse($request, $response);
    }
    protected function sendResetLinkResponse($response, $email)
    {
        return response()->json([
            'success' => true,
            'redirect' => true,
            'message' => trans($response) . '  ' . $email,
        ], $this->successStatus);
    }
    protected function sendResetLinkFailedResponse(ForgotPasswordRequest $request, $response)
    {
        return response()->json([
            'success' => false,
            'message' => sprintf(trans($response), request()->email),
        ], $this->successStatus);
    }
    //defining which guard to use in our case, it's the organizer guard
    protected function guard()
    {
        return Auth::guard('organizer');
    }
    //defining our password broker function
    protected function broker()
    {
        return Password::broker('organizer');
    }
}
