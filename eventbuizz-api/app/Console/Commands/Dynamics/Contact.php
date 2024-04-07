<?php

namespace App\Console\Commands\Dynamics;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Helpers\DynamicsCRM\DynamicsContactHelper;
use App\Models\AddAttendeeLog;
use App\Models\DynamicsToken;
use Illuminate\Console\Command;

class Contact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dynamics:contact';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync attendees with Microsoft Dynamics Contacts';

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

        $tokens = DynamicsToken::all();

        foreach ($tokens as $token) {
            $organizer_id = $token->organizer_id;
            $dmContactHelper = new DynamicsContactHelper($organizer_id);

            $attendees = AttendeeRepository::getAttendeeLog($organizer_id);

            foreach ($attendees as $attendee) {
                $success = $dmContactHelper->upsert($attendee);
                $attendeeLog = AddAttendeeLog::find($attendee['log_id']);
                if($success !== false){
                    $attendeeLog->status = 1;
                }else{
                    $attendeeLog->status = 2;
                }
                $attendeeLog->save();
            }
        }
        $this->info("Attendee Synced!");
    }
}
