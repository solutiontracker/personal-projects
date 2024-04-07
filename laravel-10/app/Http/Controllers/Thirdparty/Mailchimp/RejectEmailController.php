<?php

namespace App\Http\Controllers\Thirdparty\Mailchimp;

use App\Eventbuizz\Repositories\AttendeeRepository;

use Illuminate\Http\Request;

use Illuminate\Support\Str;

use App\Http\Controllers\Controller;

class RejectEmailController extends Controller
{
    protected $mailchimp;
    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->mailchimp = new \MailchimpTransactional\ApiClient();

        $this->mailchimp->setApiKey('wgFBWfLx5E8Yg87EQQiakw');

        exit;
    }


    public function delete_rejection_denylist() {
        $response = $this->mailchimp->rejects->list();

        $emails = array();

        if(count($response) > 0) {
            foreach($response as $key => $row) {
                if(Str::endsWith($row->email, '.no') && !$row->deleted) {
                   $response = $this->mailchimp->rejects->delete(["email" =>  $row->email]);
                    $emails[] = $row->email;
                }
            }
        }

    }
}
