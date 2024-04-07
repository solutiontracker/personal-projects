<?php

namespace App\Console\Commands\Salesforce;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Helpers\SalesForce\SalesForceContactHelper;
use App\Helpers\SalesForce\SalesForceHelper;
use App\Models\AddAttendeeLog;
use App\Models\Integration;
use App\Models\Organizer;
use App\Models\SalesforceToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncAttendee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salesforce:syncAttendee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync attendee information with salesforce contact object';

    public $alias = 'salesforce';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

       $salesforce = new SalesForceHelper();
       $salesforce->syncAttendees();
    }

}
