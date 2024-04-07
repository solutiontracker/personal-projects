<?php

namespace App\Http\Controllers\Wizard;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
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
     * @param Request $request
     * @param int $page
     *
     * @return JsonResponse [type]
     */
    public function listing(Request $request, int $page = 1): JsonResponse
    {

        $event = $request->event;

        $request->merge([
            "page" => $page,
            'registration_form_id'=> $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0,
        ]);

        $orders = $this->eventsiteBillingOrderRepository->getOrders($request->all());

        $permissions = [
            "add" => $this->organizerRepository->getOrganizerPermissionsModule('eventsite_registration', 'add')
        ];

        return response()->json([
            'success' => true,
            'data' => $orders,
            'permissions' => $permissions,
        ], $this->successStatus);

    }

    /**
     * @param Request $request
     * @param mixed $page
     *
     * @return JsonResponse [type]
     */
    public function orders(Request $request, int $page = 1): JsonResponse
    {

        $event = $request->event;

        $request->merge([
            "page" => $page,
            'registration_form_id'=> $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0,
        ]);

        $response = $this->eventsiteBillingOrderRepository->getOrders($request->all());

        return response()->json([
            'success' => true,
            'data' => $response,
        ], $this->successStatus);
        
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function export(Request $request)
    {
        if ($request->isMethod('POST')) {
            return Excel::download(new OrdersExport($request, 'order-list'), 'orders.xlsx');
        }
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function export_detail(Request $request)
    {
        if ($request->isMethod('POST')) {
            return Excel::download(new OrdersExport($request, 'order-list-detail'), 'orders.xlsx');
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function cancel_order(Request $request, $id)
    {
        $option = $request->get('option');

        if ($request->isMethod('POST')) {
            $client = new Client();
            $url = cdn('/_admin/wizard/billing/order_history/');

            if ($option === '1') {
                $url .= "cancel_order_and_send_credit_note";
            } else if ($option === '2') {
                $url .= 'cancel_order';
            } else if ($option === '3') {
                $url .= "cancel_order_without_credit_note";
            }

            $url .= "/$id?output=json&event_id=" . $request->get('event_id');

            try {
                $response = $client->request('get', $url );
                return json_encode(['success' => true]);
            }
            catch(\Exception $e){
                return json_encode(['error' => "Server error", 'success' => false]);
            }
        }
    }

    /**
     * Waiting List Orders
     * @param Request $request
     * @param int $page
     * @return JsonResponse
     */
    public function waitingListOrders(Request $request, int $page = 1)
    {
        $event = $request->event;

        $request->merge([
            "is_waiting_list" => true,
            "page" => $page,
            'registration_form_id'=> $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0,
        ]);

        $eventId = $request->input("event_id");

        //waiting list orders
        $waitingListOrders = EventsiteBillingOrderRepository::getOrders($request->all());

        $totalEventTickets = EventsiteBillingOrderRepository::getTotalTicketsForEvent($request->all());

        $confirmedAttendeesCount = EventsiteBillingOrderRepository::getConfirmedEventAttendeesCount($request->all());

        $offerLettersCount = EventsiteBillingOrderRepository::getSentOfferLettersCount($request->all());
        
        $pendingAttendeesCount = EventsiteBillingOrderRepository::getPendingAttendeesCount($request->all());

        $attending = EventsiteBillingOrderRepository::getCountOfAttendingUsers($request->all());
        
        $notInterested = EventsiteBillingOrderRepository::getNotInterestedUsersCount($request->all());

        //remaining tickets
        if($totalEventTickets == 0 || $totalEventTickets == '') {
            $remainingTickets = 0;
        }else{
            $remainingTickets = $totalEventTickets - ($confirmedAttendeesCount + $offerLettersCount);
        }

        return response()->json([
            'attending' => $attending,
            'remaining_tickets' => $remainingTickets,
            'pending_tickets' => $pendingAttendeesCount,
            'offer_letters' => $offerLettersCount,
            'confirmed_attendees' => $confirmedAttendeesCount,
            'not_interested' => $notInterested,
            'waitingListOrders' => $waitingListOrders
        ]);
    }

    /**
     * send offer to waiting list attendees
     * @param $orderId
     * @return void
     * @throws \Exception
     */
    public function sendOffer(Request $request, $orderId): JsonResponse
    {
        $request->merge([
            "order_id" => $orderId
        ]);
        $order_detail = EventsiteBillingOrderRepository::getOrder($request->all());
        $attendee = $order_detail['order_attendee'];

        $request->merge([
            "status" => 1,
            "attendee" => $attendee
        ]);

        $total_waiting_attendees = EventsiteBillingOrderRepository::getOrderAttendeesCount($request->all());
        $confirmed_attendees = EventsiteBillingOrderRepository::getEventAttendeesCount($request->all());
        $offer_letters = EventsiteBillingOrderRepository::getSentOfferLettersCount($request->all());
        $event_tickets = EventsiteBillingOrderRepository::getTotalEventTickets($request->all());

        if ($event_tickets == 0 || $event_tickets == '') {
            $total_tickets_remaining = 0;
            if (EventsiteBillingOrderRepository::canSendOfferLetter($request->all())) {
                EventsiteBillingOrderRepository::emailOfferLetter($request->all());
                EventsiteBillingOrderRepository::updateWaitingAttendeeStatus($request->all());
                return response()->json([
                    "message" => "Successfully sent offer letter.",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Offer has already been sent.",
                ], 200);
            }
        } else {
            $total_tickets_remaining = $event_tickets - ($confirmed_attendees + $offer_letters);
            if ($total_tickets_remaining >= $total_waiting_attendees) {
                if (EventsiteBillingOrderRepository::canSendOfferLetter($request->all())) {
                    EventsiteBillingOrderRepository::emailOfferLetter($request->all());
                    EventsiteBillingOrderRepository::updateWaitingAttendeeStatus($request->all());
                    return response()->json([
                        "message" => "Successfully sent offer letter.",
                    ], 200);
                } else {
                    return response()->json([
                        "message" => "Offer has already been sent.",
                    ], 200);
                }
            } else {
                return response()->json([
                    "message" => "Not enough tickets to send offer letter.",
                ], 403);
            }
        }
    }

    /**
     * delete the order from waiting list
     * @param Request $request
     * @param $orderId
     * @return JsonResponse
    */
    public function deleteOrder(Request $request, $orderId): JsonResponse
    {
        $request->merge([
            "order_id" => $orderId
        ]);

        $request->validate([
            "order_id" => "required"
        ]);

        //delete the record
        try {
            EventsiteBillingOrderRepository::deleteOrder($request->all());
            return response()->json([
                "message" => "Order is deleted!"
            ], 200);
        } catch (\Throwable $exception) {
            return response()->json([
                "message" => "Server error"
            ], 500);
        }
    }
    
    public function sendOrderEmail(Request $request, $order_id)
    {
        try {

            $request->merge([
                "panel" => "admin", 'is_new_flow' => 1,
            ]);
            
            $order = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

            $order->loadTicketsIds();

            $this->eventsiteBillingOrderRepository->generatePdfForTicketItems($order);

            $this->eventsiteBillingOrderRepository->registerConfirmationEmail($order);

            return response()->json([
                "success" => true,
                "message" => "Order send successfully..."
            ], 200);   
        } catch (\Throwable $th) {
            return response()->json([
                "success" => true,
                "message" => "Server error"
            ], 200);   
        }
    }
    
    /**
     * orderSummary
     *
     * @param  mixed $request
     * @param  mixed $orderId
     * @return void
     */
    public function orderSummary(Request $request, $orderId) {

        $order = new \App\Eventbuizz\EBObject\EBOrder([], $orderId);
	
		$html = $this->eventsiteBillingOrderRepository->orderAction($order, 1, 'html');

        return response()->json([
            "success" => true,
            "data" => array(
                "html" => $html
            )
        ], 200);
    }
    
    /**
     * sendOrder
     *
     * @param  mixed $request
     * @param  mixed $orderId
     * @return void
     */
    public function sendOrder(Request $request, $orderId){

        $order = new \App\Eventbuizz\EBObject\EBOrder([], $orderId);
	
        $order->loadTicketsIds();

        $this->eventsiteBillingOrderRepository->generatePdfForTicketItems($order);

		$this->eventsiteBillingOrderRepository->registerConfirmationEmail($order);

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
    public function sendOrderPdf(Request $request, $orderId) {
        
        $order = new \App\Eventbuizz\EBObject\EBOrder([], $orderId);
	
		return $this->eventsiteBillingOrderRepository->orderAction($order, 1, 'download-pdf');
    }
    
    /**
     * sendEan
     *
     * @param  mixed $request
     * @param  mixed $order_id
     * @return void
     */
    public function sendEan(Request $request, $order_id)
    {
        $request->merge([
            "panel" => "admin", 'is_new_flow' => 1,
        ]);
        
        $order = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

        $order->loadTicketsIds();

        $this->eventsiteBillingOrderRepository->generatePdfForTicketItems($order);

        $xml = $this->eventsiteBillingOrderRepository->sendXml($order);

        $response = $this->eventsiteBillingOrderRepository->sendXmlEmail($order, $xml);

        return response()->json($response, 200);
    }
    
}
