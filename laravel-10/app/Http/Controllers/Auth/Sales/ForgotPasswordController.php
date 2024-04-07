<?php

namespace App\Http\Controllers\Auth\Sales;

use App\Http\Controllers\Auth\Sales\Requests\VerifyResetCodeRequest;
use App\Http\Services\Sales\SalesAuthServices;
use App\Eventbuizz\Repositories\Sales\SaleAgentRepository;
use App\Http\Controllers\Auth\Sales\Requests\ForgotPasswordRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Sales\Helpers\SalesAgentAuthHelper;
use App\Http\Helpers\HttpHelper;
use App\Models\PasswordReset;
use Illuminate\Http\JsonResponse;

class ForgotPasswordController extends Controller
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
    public function forgotPassword(ForgotPasswordRequest $request) {
        try {
            $agent = $this->saleAgentRepository->getAgentByColumn('email', $request->email);
            if (!$agent) {
                return HttpHelper::errorJsonResponse(str_replace('%s', $request->email, __('passwords.user')));
            }
            $resetToken = SalesAgentAuthHelper::generateRandonSixDigits();
            SalesAuthServices::updateResetCode($request->email, $resetToken);
            // trigger reset password email
            SalesAuthServices::sendResetPasswordEmail($agent, $resetToken);
            return HttpHelper::successJsonResponse('Reset password verification code is sent to your email');
        } catch (\Exception $e) {
            return HttpHelper::exceptionJsonResponse($e);
        }
    }


    /**
     * @param VerifyResetCodeRequest $request
     *
     * @return JsonResponse
     */
    public function verifyResetCode(VerifyResetCodeRequest $request) {
        try {
            $resetPassword = PasswordReset::where(['token' => $request->token, 'email' => $request->email])
                ->first();
            if (!$resetPassword) {
                return HttpHelper::errorJsonResponse(__('passwords.token'));
            }

            return HttpHelper::successJsonResponse('Reset password code verified', 'Success', ['resetCode' => $request->token, 'email' => $resetPassword->email]);
        } catch (\Exception $e) {
            return HttpHelper::exceptionJsonResponse($e);
        }
    }

}
