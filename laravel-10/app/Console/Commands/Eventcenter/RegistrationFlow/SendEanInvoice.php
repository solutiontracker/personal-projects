<?php

namespace App\Console\Commands\Eventcenter\RegistrationFlow;

use Illuminate\Console\Command;

use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;

use App\Mail\Email;

class SendEanInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RegistrationFlow:SendEanInvoice';

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

        $event_ids = \App\Models\EventsitePaymentSetting::join('conf_events', 'conf_events.id', '=', 'conf_eventsite_payment_settings.event_id')->where('conf_eventsite_payment_settings.auto_invoice', 1)->where('conf_eventsite_payment_settings.registration_form_id', 0)->where('conf_events.registration_form_id', 1)->pluck('conf_eventsite_payment_settings.event_id');

        if(count($event_ids) > 0) {

            $valid_order_ids = \App\Models\BillingOrder::whereIn('event_id', $event_ids)->where('order_type', 'invoice')->where('e_invoice', '0')->whereDate('created_at', '>=', \Carbon\Carbon::now()->subdays(2))->where('status', 'completed')->where('is_archive', 0)->currentActiveOrders()->pluck('id');

            if(count($valid_order_ids) > 0) {

                $result = \App\Models\BillingOrder::join('conf_attendee_billing', 'conf_attendee_billing.order_id', '=', 'conf_billing_orders.id')->whereIn('conf_billing_orders.id', $valid_order_ids)->where('conf_attendee_billing.billing_company_type', 'public')->select('conf_billing_orders.*', 'conf_attendee_billing.billing_company_type')->orderBy('conf_billing_orders.id', 'desc')->limit('20')->get();
    
                if(count($result) > 0) {
        
                    foreach ($result as $row) {

                        if ($row->billing_company_type == 'public') {

                            try {

                                $event =  \App\Models\Events::find($row->event_id);

                                request()->merge([
                                    "panel" => "admin", 'is_new_flow' => 1, 'event_id' => $event->id, 'language_id' => $event->language_id, 'organizer_id' => $event->organizer_id
                                ]);

                                $order = new \App\Eventbuizz\EBObject\EBOrder([], $row->id);

                                $order->loadTicketsIds();

                                $this->eventsiteBillingOrderRepository->generatePdfForTicketItems($order);

                                $xml = $this->eventsiteBillingOrderRepository->sendXml($order);

                                $response = $this->eventsiteBillingOrderRepository->sendXmlEmail($order, $xml);

                            } catch (\Exception $e) {
                                
                                //email
                                $data = array();

                                $data['subject'] = "Send auto ean error for this order (".$row->id.")";

                                $data['content'] = $e->getMessage();

                                $data['bcc'] = ['ki@eventbuizz.com', 'ida@eventbuizz.com'];

                                $data['view'] = 'email.plain-text';
                                
                                \Mail::to('support@eventbuizz.com')->send(new Email($data));

                            }

                        }
                    }
                }

            }
        
        }

        $this->info('Send invoices successfully!');

    }
}
