<?php

namespace App\Http\Controllers\Sales;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;
use App\Eventbuizz\Repositories\OrganizerRepository;
use App\Exports\Order\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;

class EventsiteBillingOrderController extends Controller
{
    public $successStatus = 200;

    protected $eventsiteBillingOrderRepository;
    
    protected $organizerRepository;

    /**
     * @param EventsiteBillingOrderRepository $eventsiteBillingOrderRepository
     * @param OrganizerRepository $organizerRepository
     */
    public function __construct(EventsiteBillingOrderRepository $eventsiteBillingOrderRepository, OrganizerRepository $organizerRepository)
    {
        $this->eventsiteBillingOrderRepository = $eventsiteBillingOrderRepository;
        $this->organizerRepository = $organizerRepository;
    }

    /**
     * delete the order from waiting list
     * @param Request $request
     * @param $orderId
     * @return JsonResponse
    */
    public function deleteOrder(Request $request, $event_id, $orderId): JsonResponse
    {
        $request->merge([
            "order_id" => $orderId,
            "event_id" => $event_id
        ]);

        $request->validate([
            "order_id" => "required",
            "event_id" => "required"
        ]);

        //delete the record
        try {

            EventsiteBillingOrderRepository::deleteOrder($request->all());

            return response()->json([
                "message" => "Order is deleted!"
            ], 200);

        } catch (\Throwable $exception) {

            return response()->json([
                "message" => $exception->getMessage()
            ], 500);
            
        }
    }
    
    /**
     * sendOrder
     *
     * @param  mixed $request
     * @param  mixed $orderId
     * @return void
     */
    public function sendOrder(Request $request, $orderId){

        $order = EventsiteBillingOrderRepository::getOrder(['order_id' => $orderId]);
        
        request()->merge([
            'event_id' => $order['event_id'],
            'language_id' => $order['language_id']
        ]);

        $order = new \App\Eventbuizz\EBObject\EBOrder([], $orderId);
	
        $order->loadTicketsIds();

        $this->eventsiteBillingOrderRepository->generatePdfForTicketItems($order);

		$this->eventsiteBillingOrderRepository->registerConfirmationEmail($order, 1);

        return response()->json([
            "success" => true,
            "message" => "Send order successfully.",
        ], 200);

    }
    
    /**
     * sendOrderPdf
     *
     * @param  mixed $request
     * @param  mixed $orderId
     * @return void
     */
    public function sendOrderPdf(Request $request, $orderId, $order_type = 'invoice') {
        
        $order = EventsiteBillingOrderRepository::getOrder(['order_id' => $orderId]);
        
        request()->merge([
            'order_type' => $order_type,
            'event_id' => $order['event_id'],
            'language_id' => $order['language_id']
        ]);
        
        $order = new \App\Eventbuizz\EBObject\EBOrder([], $orderId);

        $order->loadTicketsIds();

        $this->eventsiteBillingOrderRepository->generatePdfForTicketItems($order);

		return $this->eventsiteBillingOrderRepository->orderAction($order, 1, 'download-pdf');
    }
    
        
    /**
     * setPaymentRecievedStatus
     *
     * @param  mixed $request
     * @param  mixed $orderId
     * @return void
     */
    public function changePaymentRecievedStatus(Request $request, $order_id) {

        $EBOrder = \App\Models\BillingOrder::where('id', $order_id)->update([
            'is_payment_received' => $request->payment_status,
            'payment_received_date' => (isset($request->date) && $request->date !== '') ? $request->date : \Carbon\Carbon::now(),
        ]);

        return response()->json([
            "success" => true,
            "message" => "payment status set successfully.",
        ], 200);
	
    } 
}
