<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Eventbuizz\Repositories\EventsiteRepository;

class EventSiteBillingController extends Controller
{
    public $successStatus = 200;

    protected $eventsiteBillingOrderRepository;

    public function __construct( EventsiteBillingOrderRepository $eventsiteBillingOrderRepository)
    {
        $this->eventsiteBillingOrderRepository = $eventsiteBillingOrderRepository;
    }

   /**
     * getOrderInvoice
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $attendee_id
     * @return void
     */
    public function getOrderInvoice(Request $request, $slug) {

        //Get order
        try {

            $order = EventsiteBillingOrderRepository::getOrderfromEventAttendeeIds($request->event['id'], $request->user()->id);

            $order_id = $order ?  $order->id  : null;
            
            if($order_id === null){
                return \Response::json([
                    "message" => 'No order found',
                    "success" => true
                ]);
            }

            $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

            // Order detail summary
            $order_detail = $EBOrder->getInvoiceSummary();

            //labels
            $labels = $request->event['labels'];

            $event_id = $EBOrder->getOrderEventId();

            $language_id = $EBOrder->getUtility()->getLangaugeId();

            $payment_setting = $EBOrder->_getPaymentSetting();

            $eventsite_setting = $EBOrder->_getEventSiteSetting();

            $billing_currency = $payment_setting['eventsite_currency'];
            
            $invoice = $this->eventsiteBillingOrderRepository->getOrderDetailInvoice("html", $EBOrder, $labels, $language_id, $event_id, $billing_currency, $order_detail['order']['id'],1, 1, true, false, 0, 0, true);
            
            $is_invoice_update = $this->eventsiteBillingOrderRepository->isInvoiceUpdate($eventsite_setting);

            return \Response::json([
                "data" => [
                    'order_id' => $order_id,
                    'invoice' => $invoice,
                    'is_invoice_update' => $is_invoice_update ? 1 : 0
                ],
                "success" => true
            ]);

        } catch (\Exception $e) {
            return \Response::json([
                "message" => "Server error",
                "success" => false
            ]);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function cancel_order(Request $request, $slug)
    {
        $order = EventsiteBillingOrderRepository::getOrderfromEventAttendeeIds($request->event['id'], $request->user()->id);

        $order_id = $order ?  $order->id  : null;

        $settings = EventsiteRepository::getSetting(['event_id' => $request->event['id'], 'registration_form_id' => 0]);

        $is_cancel = EventsiteRepository::getCancelStatus($order, $settings);

        if($is_cancel) {   

            $request->merge(['order_id' =>  $order_id, 'attendee_id' => $request->user()->id, 'create_credit_note' => 1, 'send_credit_note' => 1]); 

            $data = EventsiteBillingOrderRepository::cancelOrder($request->all());

            return json_encode(['success' => $data['success']]);

        } else {

            return json_encode(['success' => false, "message"=> "Cancellation date/time has been expired."]);
            
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function unsubscribeAttendee(Request $request)
    {
        $response = $this->eventsiteBillingOrderRepository->unsubscribeAttendee($request->all(), request()->isMethod('POST'));
        return \Response::json($response);   
    }

}
