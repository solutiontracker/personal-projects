<?php

namespace App\Http\Controllers\Auth\ReportingAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\ReportingAgent\Requests\LoginRequest;
use App\Http\Controllers\Auth\ReportingAgent\Requests\ForgotPasswordRequest;
use App\Http\Controllers\Auth\ReportingAgent\Requests\ResetPasswordRequest;
use App\Http\Controllers\Auth\ReportingAgent\Requests\VerificationRequest;
use App\Models\ReportingAgent;
use \App\Mail\Email;
use Illuminate\Support\Facades\Hash;

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
        $user = ReportingAgent::where('email', $request->email)->whereNull('deleted_at')->first();

        if($user->status === 'n'){
            return response()->json([
              'success' => 0,  
              'message' => __('auth.inactive')
            ], $this->successStatus);
        }
        
        if(!\Hash::check($request->password, $user->password)){
            return response()->json([
              'success' => 0,  
              'message' => __('auth.failed')
            ], $this->successStatus);
        }

        if($user->tokens !== null){
            $user->tokens->each(function($token, $key) {
                $token->revoke();
                $token->delete();
            });
        }

        $tokenResult = $user->createToken("reporting_agent");
        $token = $tokenResult->token;
        $token->expires_at =  $request->remember_me   ? \Carbon\Carbon::now()->addWeeks(1) : \Carbon\Carbon::now()->addHours(1);
        $token->save();
    
        $agent = $user->toArray();

        unset($agent['tokens']);
        unset($agent['password']);

        return response()->json([
            'success' => 1, 
            'message' => 'User logged in successfully' ,
            'data' => [
                'agent' => array_merge($agent, [
                    'access_token' => $tokenResult->accessToken,
                    'autologin_token' => $token->id,
                    'token_type' => 'Bearer',
                    'expires_at' => \Carbon\Carbon::parse(
                            $tokenResult->token->expires_at
                        )->toDateTimeString(),
                ])
            ]
          ], $this->successStatus);
           
            
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens->each(function($token, $key) {
            $token->revoke();
            $token->delete();
        });

        return response()->json([
            'success' => true,
        ]);
    }
         
    /**
     * forgotPassword
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        
        $user = ReportingAgent::where('email', $request->email)->whereNull('deleted_at')->first();
    
        if(!$user)
        {
        return response()->json([
            'success' => 0, 
            'response_type' => USER_NOT_FOUND, 
            'message' => str_replace('%s', $request->email, __('passwords.user')) 
        ], $this->successStatus);
        }

    
        $token = rand(100000, 999999);
        \App\Models\PasswordReset::create([
          'email' => $request->email,
          'token' => $token,
        ]);



        $data = array();
		$data['subject'] = 'Authentication Code';
        $data['view'] = 'email.plain-text';
		$data['content'] = "Authentication code for password reset for Eventbuizz reporting portal is = " . $token;
		$data['email'] = $request->email;
		\Mail::to($request->email)->send(new Email($data));

        $response = [
          "success" => 1,
          "response_type" => AUTH_VERIFICATION_SEND_TO_EMAIL,
          "message" => "For verification purpose, a code has been sent to your email address and should arrive in your email inbox momentally. Please check your email and, once received, enter the code.",
        ];    
    
        return response()->json($response, $this->successStatus);
    }
    
    /**
     * verifyResetPasswordCode
     *
     * @param  mixed $request
     * @return void
     */
    public function verifyResetPasswordCode(VerificationRequest $request)
    {
        $resetPassword = \App\Models\PasswordReset::where(['token' => $request->token, 'email' => $request->email])->first();
        if(!$resetPassword)
        {
            return response()->json([
                'success' => 0, 
                'message' => __('passwords.token')
            ], $this->successStatus);
        }

        return response()->json([
            'success' => 1,  
            'message' => "Password reset code validated successfully",
            'data'=> [
                'resetCode' => $request->token,
                'email' => $request->email 
            ]
        ], $this->successStatus);
    }
    
    /**
     * resetPassword
     *
     * @param  mixed $request
     * @return void
     */
    public function resetPassword(ResetPasswordRequest $request)
    {

            $resetPassword = \App\Models\PasswordReset::where(['token' => $request->reset_code, 'email' => $request->email])->first();
            
            if (!$resetPassword) {
                return response()->json([
                    'success' => 0,  
                    'message' => __('passwords.token')
                ], $this->successStatus);
            }

            $user = ReportingAgent::where('email', $request->email)->whereNull('deleted_at')->first();

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            \App\Models\PasswordReset::where('email', $request->email)->delete();

            // update OAuth access-token of User
            $user->tokens->each(function($token, $key) {
                $token->revoke();
                $token->delete();
            });

            return response()->json([
                'success' => 1,  
                'message' => __('passwords.reset')
            ], $this->successStatus);
        
    }
}
