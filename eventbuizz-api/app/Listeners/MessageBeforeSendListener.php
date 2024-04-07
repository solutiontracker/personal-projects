<?php

namespace App\Listeners;

use App\Jobs\EmailLog;
use App\Models\AttendeeInviteStats;
use App\Models\EmailList;
use App\Models\MailingListCampaign;
use App\Models\MailingListSubscriber;
use App\Models\TemplateCampaign;
use Illuminate\Mail\Events\MessageSending;

class MessageBeforeSendListener
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param MessageSending $event
     */
    public function handle(MessageSending $event)
    {
        $toAddress = key($event->message->getTo());
        $email_array =  explode('@', $toAddress);
        $email = EmailList::whereEmail($toAddress)->first();
        if((!$email || ($email->bounced!=1 && $email->is_active!=0)) && $email_array[1]!='nomail.com'){
            return $event;
        }else{
            $message = $event->message;

            $to = !$message->getHeaders()->get('To') ? null : $message->getHeaders()->get('To')->getFieldBody();

            $from = !$message->getHeaders()->get('From') ? null : $message->getHeaders()->get('From')->getFieldBody();

            $cc = !$message->getHeaders()->get('Cc') ? null : $message->getHeaders()->get('Cc')->getFieldBody();

            $bcc = !$message->getHeaders()->get('Bcc') ? null : $message->getHeaders()->get('Bcc')->getFieldBody();

            $subject = !$message->getHeaders()->get('Subject') ? null : $message->getHeaders()->get('Subject')->getFieldBody();

            $headers = !$message->getHeaders()->get('X-MC-Metadata') ? null : json_decode($message->getHeaders()->get('X-MC-Metadata')->getFieldBody());

            $template = isset($event->data['data']['template']) ? $event->data['data']['template'] : '';

            $body = $message->getBody();
            $attachments = request()->attachments;
            if (isset($headers->ml_campaign_id)) {
                $ml_campaign_id =$headers->ml_campaign_id;
                MailingListCampaign::where('id', '=', $ml_campaign_id)->update(['hard_bounce' => \DB::raw('hard_bounce' . '+1')]);
                $mailingListCampaign = MailingListCampaign::find($ml_campaign_id);
                $mailing_list_id = $mailingListCampaign->mailing_list_id;
                $mailingListSubscribers = MailingListSubscriber::where('mailing_list_id',$mailing_list_id)->where('email',$to)->first();
                $mailingListSubscribers->bounced = 1;
                $mailingListSubscribers->save();

            }
            if (isset($headers->campaign_id))
            {
                TemplateCampaign::where('id', '=', $headers->campaign_id)->update(['hard_bounce' => \DB::raw('hard_bounce' . '+1')]);
            }
            if (isset($headers->template) && isset($headers->email))
            {
                AttendeeInviteStats::where('template_alias', $headers->template)->where('event_id', request()->event_id)->where('email', $to)->update(['hard_bounce' => \DB::raw('hard_bounce' . '+1')]);
            }

            return false;
        }
    }
}