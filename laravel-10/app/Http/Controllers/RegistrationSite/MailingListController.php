<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MailingListController extends Controller
{
    public $successStatus = 200;

    protected $alertRepository;

    public function __construct()
    {
         
    }

    public function getMailingListSubscriberForm(Request $request, $slug, $ml_id){
        $ml_id1 = \Crypt::decrypt($ml_id);
        $response = Http::get(config('app.eventcenter_url').'/_admin/webservices/getMailingListSubscriberFormApi/'.$ml_id1);
        return $response->body(); 
    }

    public function subscribeToMailingList(Request $request, $slug, $ml_id)
    {
        $ml_id = \Crypt::decrypt($ml_id);

        $request->validate([
            "email" => 'required|email|unique:\App\Models\MailingListSubscriber,email',
            "first_name" => 'required|string|max:255',
            "checked" => 'boolean',
        ]);

        $mailingList = \App\Models\MailingList::find($ml_id);

            $formData = [
                'organizer_id' => $mailingList->organizer_id,
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'is_checked' => $request->is_checked ? 1 : 0,
                'mailing_list_id' => $mailingList->id,
            ];

            \App\Models\MailingListSubscriber::create($formData);

            return \Response::json(array("status" => 1, "message" => "You have been added to the mailing list"), 200);

    }

}
