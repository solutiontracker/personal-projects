<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EmailLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * @return void
     */

    public $request;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1200;

    public function __construct($request)
    {
        $this->onConnection('api_email_log');
        $this->request = $request;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {

        $from = $this->request['from'];

        $cc = $this->request['cc'];

        $bcc = $this->request['bcc'];

        $subject = $this->request['subject'];

        $headers = $this->request['headers'];

        $template = $this->request['template'];

        $body = $this->request['body'];

        $attachments = $this->request['attachments'];

        $sparkpost_transaction_id = $this->request['sparkpost_transaction_id'];

        $to = $this->request['to'];
        
        $event_id = $this->request['event_id'];  
        
        $response = $this->request['response']; 
        
        $organizer_id = $this->request['organizer_id'];

        $log = \App\Models\EmailStatsLog::where("transmission_id", $sparkpost_transaction_id)->first();

        if(!$log) {
            $log = (\App\Models\EmailStatsLog::create([
                'event_id' => $event_id,
                'organizer_id' => $organizer_id,
                'transmission_id' => $sparkpost_transaction_id,
                'to' => $to,
                'cc' => $cc,
                'bcc' => $bcc,
                'subject' => $subject,
            ]));
        } else {
            $log->event_id = $event_id;
            $log->organizer_id = $organizer_id;
            $log->to = $to;
            $log->cc = $cc;
            $log->bcc = $bcc;
            $log->subject = $subject;
            $log->save();
        }
        
        (\App\Models\EmailStatsLogInfo::create([
            'from' => $from,
            'body' => $body,
            'headers' => json_encode($headers),
            'template' => $template,
            'response' => json_encode($response),
            'email_stats_log_id' => $log->id,
        ]));
    
    
        //Save attachments
        if (is_array($attachments)) {
            if(count($attachments) > 0) {
                foreach($attachments as $attachment) {
    
                    $attachment_path = trim($attachment['path']);
    
                    if($attachment_path) {
                        \App\Models\EmailAttachment::create([
                            'email_log_id' => $log->id,
                            'filename' => end(explode('/',$attachment_path)),
                        ]);
    
                        //Save file
                        copy($attachment_path, config('cdn.cdn_upload_path').'/assets/email_attachments/'.end(explode('/', $attachment_path)));

                    }
    
                }
            }
        } else {
    
            if(trim($attachments)) {
                \App\Models\EmailAttachment::create([
                    'email_log_id' => $log->id,
                    'filename' => end(explode('/',trim($attachments))),
                ]);
    
                //Save file
                copy($attachments['path'], config('cdn.cdn_upload_path').'/assets/email_attachments/'.end(explode('/', $attachments['path'])));
            }
    
        }
    }

}
