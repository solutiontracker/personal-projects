<?php

namespace App\Console\Commands\Eventcenter;

use Illuminate\Console\Command;

use App\Mail\Email;

class EventDetectDuplicateOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
    */
    protected $signature = 'Update:EventDetectDuplicateOrder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect duplication of orders';

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
        if(\App::environment('production')) {
            $attendees = \App\Models\Attendee::having(\DB::raw('COUNT(CONCAT(email, organizer_id))'), '>', 1)->groupBy(\DB::raw('CONCAT(email, organizer_id)'))->orderBy('conf_attendees.id')->where('created_at', '>=', \DB::raw('DATE_SUB(NOW(), INTERVAL 1 HOUR)'))->pluck('id');

            if(count($attendees) > 0) {
    
                $orders = \App\Models\BillingOrderAttendee::whereIn('attendee_id', $attendees)->groupBy('order_id')->pluck('order_id');
    
                if(count($orders) > 0) {
                    //email
                    $data = array();
                    $data['subject'] = "Detect duplication of orders";
                    $data['content'] = implode(',', $orders->toArray())." orders have been duplicated please detect these orders from database using this query 'SELECT *, COUNT(CONCAT(email,organizer_id)) FROM `conf_attendees` WHERE deleted_at IS NULL GROUP BY CONCAT(email,organizer_id) HAVING COUNT(CONCAT(email,organizer_id)) > 1 ORDER BY `conf_attendees`.`id` DESC'";
                    $data['bcc'] = ['ki@eventbuizz.com', 'mms@eventbuizz.com'];
                    $data['view'] = 'email.plain-text';
                    \Mail::to('ida@eventbuizz.com')->send(new Email($data));
                } 
                
            }
    
            $this->info('Detect duplication of orders');
        }
    
    }
}
