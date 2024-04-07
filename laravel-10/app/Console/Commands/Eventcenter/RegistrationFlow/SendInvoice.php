<?php

namespace App\Console\Commands\Eventcenter\RegistrationFlow;

use Illuminate\Console\Command;

use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;

use App\Eventbuizz\Repositories\EventRepository;

class SendInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RegistrationFlow:SendInvoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * eventsiteBillingOrderRepository
     *
     * @var mixed
    */
    protected $eventsiteBillingOrderRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(EventsiteBillingOrderRepository $eventsiteBillingOrderRepository)
    {
        parent::__construct();
        $this->eventsiteBillingOrderRepository = $eventsiteBillingOrderRepository;
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $logs = \App\Models\InvoiceEmailReminderLog::where('status', 0)->where('is_new_flow', 1)->limit(50)->get();

        if(count($logs) > 0) {

            foreach($logs as $log) {

                $record = \App\Models\BillingOrder::find($log->order_id);

                if($record) {

                    $event = EventRepository::getEventDetail(['event_id' => $record->event_id]);

                    request()->merge(['event_id' => $event->id, 'language_id' => $event->language_id]);

                    $order = new \App\Eventbuizz\EBObject\EBOrder([], $log->order_id);
	
                    $order->loadTicketsIds();
            
                    $this->eventsiteBillingOrderRepository->generatePdfForTicketItems($order);
            
                    $this->eventsiteBillingOrderRepository->registerConfirmationEmail($order, 0, 'invoice_reminder_email');
    
                    $log->status = 1;
    
                    $log->save(); 

                } else {

                    $log->status = 1;
    
                    $log->save();

                }

            }

        }

        $this->info('Send invoices successfully!');

    }
}
