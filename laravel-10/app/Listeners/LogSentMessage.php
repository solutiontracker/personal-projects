<?php

namespace App\Listeners;

use App\Jobs\EmailLog;

class LogSentMessage
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
       
        $message = $event->message;
        
        $attributes = $event->data['data'];

        $to = !$message->getHeaders()->get('To') ? null : $message->getHeaders()->get('To')->getFieldBody();

        $from = (isset($attributes['from']) && $attributes['from'] ? $attributes['from'] : (!$message->getHeaders()->get('From') ? null : $message->getHeaders()->get('From')->getFieldBody()));

        $cc = !$message->getHeaders()->get('Cc') ? null : $message->getHeaders()->get('Cc')->getFieldBody();

        $bcc = !$message->getHeaders()->get('Bcc') ? null : $message->getHeaders()->get('Bcc')->getFieldBody();

        $subject = (isset($attributes['subject']) && $attributes['subject'] ? $attributes['subject'] : (!$message->getHeaders()->get('Subject') ? null : $message->getHeaders()->get('Subject')->getFieldBody()));

        $headers = !$message->getHeaders()->get('X-MC-Metadata') ? null : $message->getHeaders()->get('X-MC-Metadata')->getFieldBody();

        $template = isset($event->data['data']['template']) ? $event->data['data']['template'] : '';

        $body = $message->getBody();

        $sparkpost_transaction_id = $message->getHeaders()->get('X-SparkPost-Transmission-ID') ? $message->getHeaders()->get('X-SparkPost-Transmission-ID')->getValue() : '';
        
        $attachments = request()->attachments;

        $organizer_id = request()->organizer_id !== null ? request()->organizer_id : (isset(request()->event) && isset(request()->event->organizer_id) ? request()->event->organizer_id : null);
        
        $data = [
            'to' => $to,
            'from' => $from, 
            'subject' => $subject, 
            'body' => $body, 
            'event_id' => request()->event_id,
            'organizer_id' => $organizer_id,
            'bcc' => $bcc, 
            'cc' => $cc, 
            'headers' => $headers, 
            'template' => $template, 
            'attachments' => $attachments, 
            "response" => "",
            'sparkpost_transaction_id' => $sparkpost_transaction_id,
        ];


        EmailLog::dispatch($data);
        
    }

}
