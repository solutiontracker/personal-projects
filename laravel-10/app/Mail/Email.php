<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Email extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->subject($this->data['subject']);

        if (isset($this->data['view'])) {
            $data->view($this->data['view'])
                ->withData($this->data);
        }

        if (isset($this->data['from_name'])) {
            $data->from('donotreply@eventbuizz.com', $this->data['from_name'])->replyTo('donotreply@eventbuizz.com', $this->data['from_name']);
        }

        if (isset($this->data['bcc'])) {
            $data->bcc($this->data['bcc']);
        }

        if (isset($this->data['cc'])) {
            $data->cc($this->data['cc']);
        }

        if (isset($this->data['attachment'])) {
            foreach ($this->data['attachment'] as $key => $attahment) {
                if($attahment['path']) $data = $data->attach($attahment['path'], ['as' => $attahment['name']]);
            }
            request()->merge([
                'attachments' => $this->data['attachment']
            ]);
        }

        $data->withSwiftMessage(function ($message) {
            $mandrilL_meta_data = '{';
            if (isset($this->data['event_id'])) {
                if ($mandrilL_meta_data != '{') {
                    $mandrilL_meta_data .= ',';
                }
                $mandrilL_meta_data .= ' "event_id": "' . $this->data['event_id'] . '" ';
            }
            if (isset($this->data['template'])) {
                if ($mandrilL_meta_data != '{') {
                    $mandrilL_meta_data .= ',';
                }
                $mandrilL_meta_data .= ' "template": "' . $this->data['template'] . '" ';
            }
            if (isset($this->data['email'])) {
                if ($mandrilL_meta_data != '{') {
                    $mandrilL_meta_data .= ',';
                }
                $mandrilL_meta_data .= ' "email": "' . $this->data['email'] . '" ';
            }
            if (isset($this->data['campaign_id'])) {
                if ($mandrilL_meta_data != '{') {
                    $mandrilL_meta_data .= ',';
                }
                $mandrilL_meta_data .= ' "campaign_id": "' . $this->data['campaign_id'] . '" ';
            }
            if (isset($this->data['ml_campaign_id'])) {
                if ($mandrilL_meta_data != '{') {
                    $mandrilL_meta_data .= ',';
                }

                $mandrilL_meta_data .= ' "ml_campaign_id": "' . $this->data['ml_campaign_id'] . '" ';
            }
            $mandrilL_meta_data .= '}';
            $message->getHeaders()
                ->addTextHeader('X-MC-Metadata', $mandrilL_meta_data);
        });

        return $data;
    }
}
