<?php

namespace App\Http\Controllers\Sales\Helpers;


use App\Eventbuizz\Repositories\Sales\SaleAgentRepository;
use App\Http\Helpers\HttpHelper;
use App\Models\SaleAgent;

class SalesAgentAuthHelper
{

    /**
     * login attempt of sales agent
     *
     * @param mixed $credentials
     * @return HttpHelper::response
     */
    public static function attemptAgentLogin($credentials) {
        $userName = $credentials['email'];
        $password = $credentials['password'];

        $saleAgentRepository = app(SaleAgentRepository::class);
        $saleAgent = $saleAgentRepository->getAgentByColumn('email', $userName);
        if (!$saleAgent) {
            return HttpHelper::errorResponse(__('auth.failed'), ['agent' => $saleAgent]);
        }

        if (!password_verify($password, $saleAgent->password)) {
            return HttpHelper::errorResponse(__('auth.failed'), ['agent' => $saleAgent]);
        }

        $accountActive = self::validateAccountActiveStatus($saleAgent);
        if (!$accountActive) {
            return HttpHelper::errorResponse(__('auth.inactive'), ['agent' => $saleAgent]);
        }

        if($saleAgent->tokens !== null){
            $saleAgent->tokens->each(function($token, $key) {
                $token->revoke();
                $token->delete();
            });
        }

        return HttpHelper::successResponse('Login successful', ['agent' => $saleAgent]);
    }


    /**
     * validate account activation status
     *
     * @param SaleAgent $saleAgent
     *
     * @return boolean
     */
    public static function validateAccountActiveStatus($saleAgent) {
        if ($saleAgent->status == 'y') {
            return true;
        }

        return false;
    }


    /**
     * create unique token for reset password
     *
     * @return int
     */
    public static function generateRandonSixDigits() {
        return random_int(100000, 999999);
    }

}


