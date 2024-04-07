<?php

namespace App\Http\Helpers;


use App\Http\Controllers\Auth\Sales\Requests\LoginRequest;
use App\Models\SaleAgent;
use App\User;

class PassportOAuthHelper
{

    /**
     * create access token for saleAgent
     *
     * @param SaleAgent $saleAgent
     *
     * @return string
     */
    public static function createAccessToken($saleAgent, $remember, $tokenName = 'access') {
        $tokenResult = $saleAgent->createToken($tokenName);
        $token = $tokenResult->token;
        if ($remember) {
            $token->expires_at = \Carbon\Carbon::now()->addWeeks(1);
        }
        $token->save();

        return $tokenResult->accessToken;
    }


    /**
     * revoke authenticateable access tokens
     *
     * @param SaleAgent $authenticateable
     *
     * @return void
     */
    public static function revokeAuthenticateableTokens($authenticateable) {
        foreach ($authenticateable->tokens as $token) {
            $token->revoke();
        }

    }


    /**
     * delete access token for authenticateable
     *
     * @param SaleAgent $authenticateable
     *
     * @return void
     */
    public static function deleteAuthenticateableTokens($authenticateable) {
        $authenticateable->tokens()->delete();
    }

}
