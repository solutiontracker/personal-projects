<?php

namespace App\Http\Controllers\Auth\CRMDynamics;

use App\Helpers\DynamicsCRM\DynamicsHelper;
use App\Models\DynamicsToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    //
    /**
     * @return false|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login()
    {
        $organizer_id = auth()->user()->id;
        $organizer_token = DynamicsToken::where('organizer_id', $organizer_id)->first();

        if(!$organizer_token){
            return false;
        }

        $auth_url = DynamicsHelper::getAuthenticationUrl($organizer_token->org_url);

        return redirect($auth_url);
    }

    /**
     * @param Request $request
     * @return false|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function callback(Request $request)
    {
        if(!$request->has('code')){
            if ($request->has('error')) {
                dump($request->get('error'));
                return false;
            }
        }

        $code = $request->get('code');
        $token = DynamicsHelper::getTokenFromCode($code);

        if($token !== false) {
            $this->saveToken($token);
        }
        return redirect(env('CDN_PROTOCOL').'://'.env('CDN_URL'). '/_admin/integrations?module=integrations');
    }

    /**
     * Token object return from AzureAD
     * @param $token
     *
     */
    public function saveToken($token){
        $organizer_id = auth()->user()->id;
        DynamicsToken::where('organizer_id', $organizer_id)->update([
           'access_token' => $token->access_token,
           'refresh_token' => $token->refresh_token,
           'id_token' => $token->id_token,
           'expires_at' => Carbon::now()->addSeconds($token->expires_in),
           'authorized' => 1,
        ]);
    }
}
