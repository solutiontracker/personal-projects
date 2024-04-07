<?php


    namespace App\Console\Commands\Dynamics;


    use App\Eventbuizz\Repositories\AttendeeRepository;
    use App\Helpers\DynamicsCRM\DynamicsContactHelper;
    use App\Helpers\DynamicsCRM\DynamicsHelper;
    use App\Models\AddAttendeeLog;
    use App\Models\DynamicsToken;
    use Illuminate\Console\Command;

    class syncAttendee extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'dynamics:syncAttendee';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Sync attendees with Microsoft Dynamics';

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
            $dynamics = new DynamicsHelper();
            $dynamics->syncAttendee();
            $this->info("Dynamics Attendee Synced!");
        }
    }