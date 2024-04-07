<?php

namespace App\Http\Services\Sales;

use App\Mail\Sales\ResetPasswordEmail;
use App\Models\SaleAgent;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Models\PasswordReset;

class SalesAuthServices
{

    /**
     * send reset password code email
     *
     * @param SaleAgent|User $mailable
     *
     * @return void
     */
    public static function sendResetPasswordEmail($mailable, $resetToken) {
        Mail::to($mailable->email)->send(new ResetPasswordEmail($resetToken));
    }


    /**
     * insert reset verification record of given email
     *
     * @param string $email
     *
     * @return void
     */
    public static function updateResetCode($email, $resetToken) {
        PasswordReset::updateOrInsert(['email' => $email], [
            'email' => $email,
            'token' => $resetToken,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

}
