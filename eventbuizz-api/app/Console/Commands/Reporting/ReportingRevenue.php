<?php

namespace App\Console\Commands\Reporting;

use Illuminate\Console\Command;

class ReportingRevenue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reporting:update-event-revenue {organizer_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'calculate event orders revenue and update in to database';

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
        $organizer_id = $this->argument('organizer_id');

        $events = \App\Models\Event::with(['eventsiteSettings'])->where('organizer_id', $organizer_id)->whereDate('end_date', '>=', \Carbon\Carbon::now()->subMonths(6))->get();
        
        if(count($events) > 0) {

            foreach($events as $event) {

                \App\Eventbuizz\Repositories\EventsiteBillingOrderRepository::cleanReportingRevenue($event);

            }
        }

        $this->info("Revenue updated successfully.");
    }
    
    /**
     * getOrderIds
     *
     * @param  mixed $orders
     * @return void
     */
    public function getOrderIds($orders)
    {
        $ids = array();

        foreach($orders as $order) {
            $ids[] = $order->id;
        }

        return $ids;
    }
}
