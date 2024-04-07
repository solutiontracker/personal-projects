<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ApiRequestLog implements ShouldQueue
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
    public $timeout = 300;

    public function __construct($request)
    {
        $this->request = $request;
        $this->onQueue('eventbuizz_api_request_log');
        $this->onConnection('api_email_log'); 
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        $this->request['request'] = json_encode($this->request['request']);
        
        if(\App::environment('production')) {
            \App\Models\OrganizerAPIRequestLog::create($this->request);
        } else {
            DB::connection('mysql')->table('conf_organizer_api_request_log')->insert($this->request);
        }
    }

}