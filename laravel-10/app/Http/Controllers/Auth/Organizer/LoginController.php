<?php

namespace App\Http\Controllers\Auth\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\Organizer\Requests\LoginRequest;
use Carbon\Carbon;
use \App\Models\AutologinTokens;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as Input;

class LoginController extends Controller
{
    public $successStatus = 200;

    public function __construct()
    {
        $this->middleware('guest:organizer')->except('logout');
    }

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */

    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = ['email' => trans('auth.failed')];
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => implode(' ', $errors),
            ], 422);
        }
        return response()->json([
            'success' => false,
            'message' => implode(' ', $errors),
        ], $this->successStatus);
    }

    /**
     * @return [type]
     */
    protected function sendAccessDeniedLoginResponse()
    {
        return response()->json([
            'success' => false,
            'message' => __('auth.access_denied'),
        ], $this->successStatus);
    }

    /**
     * @return [type]
     */
    protected function sendLicenseExpiredLoginResponse()
    {
        return response()->json([
            'success' => false,
            'message' => __('auth.license_expired'),
        ], $this->successStatus);
    }

    /**
     * @return [type]
     */
    protected function sendFutureLicenseLoginResponse()
    {
        return response()->json([
            'success' => false,
            'message' => __('auth.license_for_future'),
        ], $this->successStatus);
    }

    /**
     * @return [type]
     */
    protected function sendLicenseNotAssignedLoginResponse()
    {
        return response()->json([
            'success' => false,
            'message' => __('auth.license_not_assigned'),
        ], $this->successStatus);
    }

    /**
     * @return [type]
     */
    protected function sendInactiveLoginResponse()
    {
        return response()->json([
            'success' => false,
            'message' => __('auth.inactive'),
        ], $this->successStatus);
    }

    /**
     * @param LoginRequest $request
     * 
     * @return [type]
     */
    public function login(LoginRequest $request)
    {
        // Will return only validated data
        $request->validated();

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
            $user = Auth::user();
            $signed_user = $user->tokens()->where('expires_at', '>=', \Carbon\Carbon::now())->where('revoked', 0)->orderBy('created_at', 'DESC')->first();
            if ($signed_user && !$request->logged) {
                $date = new Carbon($signed_user->last_access_at);
                if ($date->diffInMinutes(\Carbon\Carbon::now()) <= 60) {
                    return response()->json([
                        'success' => true,
                        'logged' => true,
                        'message' => sprintf(__('wizard.auth.login_user_already_logged_in'), $user->last_login_ip),
                    ], $this->successStatus);
                }
            }

            //revoked all token for this user
            $user->tokens()->get()->map(function ($token) {
                $token->forceDelete();
            });

            if (!$user->allow_plug_and_play_access) {
                return $this->sendAccessDeniedLoginResponse($request);
            }

            if ($user->status == 2) {
                return $this->sendInactiveLoginResponse($request);
            }

            if ($user->parent_id != 0) {
                $licence_check = \App\Models\SubAdminLicenceAssignSubAdmin::where('sub_admin_id', $user->id)->where('status','!=', 2)->orderBy('id','desc')->first();
                if ($licence_check) {
                    if ($licence_check->status == 0) {
                        return $this->sendLicenseExpiredLoginResponse($request);
                    } else if($licence_check->status == 2) {
                        return $this->sendFutureLicenseLoginResponse($request);
                    }
                } else {
                    return $this->sendLicenseNotAssignedLoginResponse($request);
                }
            }

            $tokenResult = $user->createToken(config('app.name'));
            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            //user last login ip save
            $user->last_login_ip = $request->getClientIp();
            $user->save();

            return response()->json([
                'success' => true,
                'data'    => array(
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString(),
                    'user' => array(
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'parent_id' => $user->parent_id,
                    ),
                    'inferface_language_id' => $user->language_id,
                    'language_id' => 1
                )
            ], $this->successStatus);
        } else {
            return $this->sendFailedLoginResponse($request);
        }
    }

    //defining which guard to use in our case, it's the organizer guard
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
     * Validate a token from storage and return the row.
     *
     * @param  string  $token
     * @return mixed
     */
    public function autoLogin(Request $request, $token)
    {
        $autologin = \App\Models\AutologinTokens::where('token', $token)->first();
        if ($autologin && $this->autologinValid($autologin)) {
            $user = \App\Models\Organizer::where('id', $autologin->user_id)->first();
            Auth::login($user);
            if ($user && Auth::check()) {
                $user = Auth::user();

                //revoked all token for this user
                $user->tokens()->get()->map(function ($token) {
                    $token->forceDelete();
                });

                $tokenResult = $user->createToken(config('app.name'));
                $token = $tokenResult->token;
                if ($request->remember_me)
                    $token->expires_at = Carbon::now()->addWeeks(1);
                $token->save();

                //user last login ip save
                $user->last_login_ip = $request->getClientIp();
                $user->save();

                return response()->json([
                    'success' => true,
                    'data'    => array(
                        'access_token' => $tokenResult->accessToken,
                        'token_type' => 'Bearer',
                        'expires_at' => Carbon::parse(
                            $tokenResult->token->expires_at
                        )->toDateTimeString(),
                        'user' => array(
                            'id' => $user->id,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'email' => $user->email,
                            'parent_id' => $user->parent_id,
                        ),
                        'inferface_language_id' => $user->language_id,
                        'language_id' => 1
                    )
                ], $this->successStatus);
            } else {
                return $this->sendFailedLoginResponse($request);
            }
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Determine whether the autologin token provided is valid and remove it
     * if not.
     *
     * @param  \Watson\Autologin\Interfaces\AutologinInterface  $autologin
     * @return bool
     */
    protected function autologinValid(AutologinTokens $autologin)
    {
        if (config('autologin.remove_expired')) {
            $lifetime = config('autologin.lifetime');
            if ($autologin->created_at <= Carbon::now()->subMinutes($lifetime)) {
                $autologin->delete();
                return false;
            }
        }
        return true;
    }

    public function autoLoginWeb($token)
    {

        $autologin = \App\Models\AutologinTokens::where('token', $token)->first();
        if ($autologin && $this->autologinValid($autologin)) {
            $user = \App\Models\Organizer::where('id', $autologin->user_id)->first();
            Auth::login($user);
        }
        if(Input::has('redirect')){
            return Redirect::to(Input::input('redirect'));
        }
        else {
            return Redirect::to('/login');
        }
    }
}
