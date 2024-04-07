<?php

namespace App\Console\Commands\Eventcenter\RegistrationFlow;

use Illuminate\Console\Command;

class DeleteDraftOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RegistrationFlow:DeleteDraftOrder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        $orders = \App\Models\BillingOrder::whereIn('status', ['awaiting_payment', 'draft'])->where('draft_at', '<=', \Carbon\Carbon::now()->subMinutes(30))->get();

        if(count($orders) > 0) {

            foreach($orders as $order) {

                \App\Models\BillingOrderAddon::where('order_id', $order->id)->delete();
                \App\Models\BillingOrderAttendee::where('order_id', $order->id)->delete();
                \App\Models\BillingOrderLog::where('order_id', $order->id)->delete();
                \App\Models\BillingOrderRuleLog::where('order_id', $order->id)->delete();
                \App\Models\BillingOrderVAT::where('order_id', $order->id)->delete();
                \App\Models\EventCheckInTicketOrderAddon::where('order_id', $order->id)->delete();
                \App\Models\EventOrderHotel::where('order_id', $order->id)->delete();
                \App\Models\EventOrderHotelRoom::where('order_id', $order->id)->delete();
                \App\Models\EventOrderKeyword::where('order_id', $order->id)->delete();
                \App\Models\EventOrderSubRegistrationAnswer::where('order_id', $order->id)->delete();
                \App\Models\EventHotelPerson::where('order_id', $order->id)->delete();
                \App\Models\WaitingListAttendee::where('attendee_id', $order->attendee_id)->where('event_id', $order->event_id)->delete();
                \App\Models\BillingOrder::where('id', $order->id)->delete();
                \App\Models\FormBuilderFormResult::where('order_id', $order->id)->delete();

            }
        }
       
        $this->info('Draft orders deleted successfully!');

    }
}
