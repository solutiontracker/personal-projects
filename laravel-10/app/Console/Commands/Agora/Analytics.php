<?php

namespace App\Console\Commands\Agora;

use App\Helpers\Agora\AnalyticsAPI;
use Illuminate\Console\Command;

class Analytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:agora_analytics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch agora call analytics and store them in database';

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
        $agora_analytics = new AnalyticsAPI();
        $result = $agora_analytics->syncCallAnalytics();
        dump($result);
    }
}
