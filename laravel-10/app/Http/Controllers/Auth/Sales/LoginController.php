<?php

namespace App\Http\Controllers\Auth\Sales;

// use App\Eventbuizz\Repositories\Sales\SaleAgentRepository;
use App\Http\Controllers\Auth\Sales\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Sales\Helpers\SalesAgentAuthHelper;
use App\Http\Helpers\HttpHelper;
use App\Http\Helpers\PassportOAuthHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class LoginController extends Controller
{

    /**
     * Sale agent login attempt
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request) {
        try {
            // attempt sale-agent login
            $loginAttempt = SalesAgentAuthHelper::attemptAgentLogin(['email' => $request->email, 'password' => $request->password]);
            if (!$loginAttempt['success']) {
                return HttpHelper::errorJsonResponse($loginAttempt['message']);
            }

            $saleAgent = $loginAttempt['data']['agent'];

            $accessToken = PassportOAuthHelper::createAccessToken($saleAgent, true, 'sales_agent');
            $responseData = [
                'agent' => array_merge($saleAgent->toArray(), ['access_token' => $accessToken]),
                'logged' => $request->remember,
                'redirect' => '/manage/events',
            ];

            return HttpHelper::successJsonResponse('Login successful', 'Success', $responseData);
        } catch (\Exception $e) {
            return HttpHelper::exceptionJsonResponse($e);
        }
    }
    
    public function logout(Request $request) {
        try {
           
            $saleAgent = $request->user();
            // update OAuth access-token of User
            PassportOAuthHelper::revokeAuthenticateableTokens($saleAgent);
            PassportOAuthHelper::deleteAuthenticateableTokens($saleAgent);

            $responseData = [
                'message' => "User logged out successfully...",
            ];

            return HttpHelper::successJsonResponse('Logout successful', 'Success', $responseData);

        } catch (\Exception $e) {
            return HttpHelper::exceptionJsonResponse($e);
        }
    }

}


