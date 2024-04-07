<?php
namespace App\Http\Controllers\RegistrationFlow;

use App\Eventbuizz\Repositories\AttendeeRepository;

use App\Http\Controllers\Controller;

use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;

use App\Eventbuizz\Repositories\EventsiteRepository;

use Illuminate\Http\Request;

use App\Events\RegistrationFlow\Event;

class ManageOrderController extends Controller
{
    public $successStatus = 200;

    protected $attendeeRepository;

    private $eventsiteBillingOrderRepository;
    
    /**
     * __construct
     *
     * @param  mixed $attendeeRepository
     * @param  mixed $eventsiteBillingOrderRepository
     * @return void
     */
    public function __construct(AttendeeRepository $attendeeRepository, EventsiteBillingOrderRepository $eventsiteBillingOrderRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->eventsiteBillingOrderRepository = $eventsiteBillingOrderRepository;
    }
    
    /**
     * index
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function index(Request $request, $event_url, $order_id)
    {
        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

        //Active order summary mode
        $order = $EBOrder->getModel();

        $order->edit_mode = 1;
        
        $order->save();

        // Order detail summary
        $order_detail = $EBOrder->getInvoiceSummary();

        //labels
        $labels = $request->event['labels'];

        $event_id = $EBOrder->getOrderEventId();

        $language_id = $EBOrder->getUtility()->getLangaugeId();

        $payment_setting = $EBOrder->_getPaymentSetting();

        $billing_currency = $payment_setting['eventsite_currency'];

        $order = $this->eventsiteBillingOrderRepository->getOrderDetailInvoice("json", $EBOrder, $labels, $language_id, $event_id, $billing_currency, $order_detail['order']['id'],1, 1, true, false, 0, 0, true);

        $tos = EventsiteRepository::getTermAndConditions($request->all());

        $purchase_policy_line_text = $tos->purchase_policy_inline_text;

        $tos->purchase_policy_link_text =  between('{detail_link}', '{/detail_link}', $purchase_policy_line_text);

		$tos->inline_text = str_replace('{detail_link}'.$tos->purchase_policy_link_text.'{/detail_link}', '', $purchase_policy_line_text);

        // $order_attendee_docs = \App\Eventbuizz\Repositories\EventsiteDocumentRepository::getAllOrderAttendeeDocuments($order_id);

        $sale_types = $request->provider == "sale" ? EventsiteBillingOrderRepository::getSaleTypes($request->organizer_id) : [];

        return response()->json([
            'success' => true,
            'data' => array(
                "order" => $order,
                "tos" => $tos,
                "sale_types" => $sale_types
            ),
        ], $this->successStatus);
        
    }
    
    /**
     * submitOrder
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function submitOrder(Request $request, $event_url, $order_id) {

        $event = $request->event;

        $labels = $request->event['labels'];

        $setting = $request->event['eventsite_setting'];

        $security_key=$request->has('security_key')?$request->get('security_key'):'';

        $request->merge([
            "panel" => $request->provider ? $request->provider : "attendee", 'is_new_flow' => 1
        ]);

        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

        $billing_company_type = $EBOrder->getMainAttendee()->getBillingModelAttribute('billing_company_type');
        
        $EBOrder->_setStateInProgress();

        $EBOrder->setIsEdit();

        //Incase new order || update order || Waiting list order saving
        if(($EBOrder->getModel()->status == "draft" || ($EBOrder->getModel()->status == 'completed' && (($EBOrder->getModel()->is_waitinglist == 1 && $EBOrder->getModel()->security_key == $security_key) || in_array($request->provider, ['sale', 'admin'])))) && $EBOrder->getModel()->status != 'awaiting_payment' && ($EBOrder->verifyAttendees() || in_array($request->provider, ['sale', 'admin']))) {
            
            if($EBOrder->_countCompletedOrderAttendees() == count($EBOrder->getAllAttendees())) {
                
                if(((($EBOrder->getModel()->grand_total >= 0 && $EBOrder->getMainAttendee()->getBillingModelAttribute('billing_company_type') != "invoice") || $EBOrder->getMainAttendee()->getBillingModelAttribute('billing_company_type') == "invoice")) || $setting->payment_type == 0) {
                
                    if($billing_company_type || $setting->payment_type == 0 || $EBOrder->getModel()->grand_total == 0) {
    
                        //Create credit note
                        if($EBOrder->getModel()->clone_of) {
                            
                            $cloned = $EBOrder->cloneOrder($EBOrder->getModel()->clone_of, 1);

                            $EBOrder->setPreviousVersion(new \App\Eventbuizz\EBObject\EBOrder([], $cloned->id));

                            $EBOrder->updateModelAttribute('status', 'completed');
                        }

                        $EBOrder->save();
                
                        //Event trigger
                        if($request->credit_note == 1) {
                            event(Event::OrderNewCreatedWithCreditNoteInstaller, $EBOrder);
                        }
                        
                        return response()->json([
                            'success' => true,
                            'message' => "order save successfully",
                        ], $this->successStatus);
        
                    } else {
        
                        return response()->json([
                            'success' => false,
                            'errors' => array(
                                "order" => $labels['REGISTRATION_FORM_PLEASE_ADD_PAYMENT_INFO']
                            ),
                        ], $this->successStatus);
        
                    }
                    
                } else {
                    return response()->json([
                        'success' => false,
                        'errors' => array(
                            "order" => $labels['REGISTRATION_FORM_GRAND_TOTAL_GREATER_THEN_ZERO']
                        ),
                    ], $this->successStatus);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'errors' => array(
                        "order" => $labels['REGISTRATION_FORM_SOME_ATTENDEES_INCOMPLETED']
                    ),
                ], $this->successStatus);
            }
        } else {
            return response()->json([
                'success' => false,
                'errors' => array(
                    "order" => $labels['REGISTRATION_FORM_ORDER_ALREADY_PLACED']
                ),
            ], $this->successStatus);
        }
    }

    /**
     * addToCalender
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function addToCalender(Request $request, $event_url, $order_id) {

        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

        $labels = $request->event['labels'];

        $this->eventsiteBillingOrderRepository->addToCalender($EBOrder, $labels);
    }

    /**
     * cancelWaitingListOrder
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function cancelWaitingListOrder(Request $request, $event_url, $order_id) {

        $event = $request->event;

        $labels = $request->event['labels'];

        $response = $this->eventsiteBillingOrderRepository->cancelWaitingListOrder($order_id, $event['id'], $labels);

        return response()->json($response, $this->successStatus);
    }

    /**
     * cloneOrder
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function cloneOrder(Request $request, $event_url, $order_id, $platform = null) {
        
        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

        $response = $EBOrder->cloneOrder($order_id, null, $platform);

        return response()->json([
            'success' => true,
            'data' => $response,
        ], $this->successStatus);

    }

    /**
     * getOrderInvoice
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function getOrderInvoice(Request $request, $event_url, $order_id) {

        //Get order
        try {
            $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

            // Order detail summary
            $order_detail = $EBOrder->getInvoiceSummary();

            //labels
            $labels = $request->event['labels'];

            $event_id = $EBOrder->getOrderEventId();

            $language_id = $EBOrder->getUtility()->getLangaugeId();

            $payment_setting = $EBOrder->_getPaymentSetting();

            $billing_currency = $payment_setting['eventsite_currency'];
            
            return $this->eventsiteBillingOrderRepository->getOrderDetailInvoice("html", $EBOrder, $labels, $language_id, $event_id, $billing_currency, $order_detail['order']['id'],1, 1, true, false, 0, 0, true);
        } catch (\Exception $e) {
            return \Response::json([
                "message" => "Server error",
                "success" => false
            ]);
        }
    }

    /**
     * updateSaleType
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function updateSaleType(Request $request, $event_url, $order_id = null)
    {
        $event = $request->event;
        
        try {
            if ($request->isMethod('POST') && $order_id) {

                request()->merge([ "panel" => $request->provider ? $request->provider : "attendee", "draft" => true]);
                
                //Get order
                $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

                $EBOrder->_setStateInProgress();

                $EBOrder->setIsEdit();

                $EBOrder->updateModelAttribute('sale_type', $request->sale_type);
                
                $EBOrder->getModel()->save();

                return response()->json([
                    'success' => true,
                ], $this->successStatus);
            }
        } catch (\Exception $e) {
            return \Response::json([
                "message" => "Server error",
                "success" => false
            ]);
        }
    }
    
}
