<?php

namespace App\Http\Controllers\Auth\Sales;

use App\Eventbuizz\Repositories\Sales\SaleAgentRepository;
use App\Http\Controllers\Auth\Sales\Requests\ForgotPasswordRequest;
use App\Http\Controllers\Auth\Sales\Requests\ResetPasswordRequest;
use App\Http\Controllers\Controller;
use App\Http\Helpers\HttpHelper;
use App\Http\Helpers\PassportOAuthHelper;
use App\Models\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{

    protected $saleAgentRepository;

    public function __construct(SaleAgentRepository $saleAgentRepository)
    {
        parent::__construct();
        $this->saleAgentRepository = $saleAgentRepository;
    }


    /**
     * @param ForgotPasswordRequest $request
     *
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request) {
        try {
            DB::beginTransaction();
            $resetPassword = PasswordReset::where(['token' => $request->reset_code, 'email' => $request->email])
                ->with('agent')
                ->first();
            if (!$resetPassword) {
                return HttpHelper::errorJsonResponse('Authorization error');
            }

            $agent = $resetPassword->agent;
            $agent->update([
                'password' => Hash::make($request->password)
            ]);
            PasswordReset::where('token', $request->reset_code)->delete();

            // update OAuth access-token of User
            PassportOAuthHelper::revokeAuthenticateableTokens($agent);
            PassportOAuthHelper::deleteAuthenticateableTokens($agent);

            DB::commit();
            return HttpHelper::successJsonResponse(__('passwords.reset'), '', []);
        } catch (\Exception $e) {
            DB::rollBack();
            return HttpHelper::exceptionJsonResponse($e, true);
        }
    }

}
