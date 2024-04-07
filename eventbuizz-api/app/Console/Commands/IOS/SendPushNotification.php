<?php

namespace App\Console\Commands\IOS;

use App\Helpers\IOS\APNS;
use Illuminate\Console\Command;

class SendPushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:ios_notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Push Notifications to IOS devices';

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
     * @return int
     */
    public function handle()
    {
        $ios = new APNS();
        $ios->sendAllNotification();
        return 0;
    }
}
