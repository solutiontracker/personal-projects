<?php
namespace App\Http\Controllers\RegistrationFlow;

use App\Eventbuizz\Repositories\NetworkInterestRepository;

use App\Http\Controllers\Controller;

use App\Eventbuizz\Repositories\EventSiteSettingRepository;

use Illuminate\Http\Request;

use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;

class ManageKeywordController extends Controller
{
    public $successStatus = 200;

    protected $networkInterestRepository;

    private $eventsiteBillingOrderRepository;

    /**
     * @param NetworkInterestRepository $networkInterestRepository
     * @param  mixed $eventsiteBillingOrderRepository
     */
    public function __construct(NetworkInterestRepository $networkInterestRepository, EventsiteBillingOrderRepository $eventsiteBillingOrderRepository)
    {
        $this->networkInterestRepository = $networkInterestRepository;
        $this->eventsiteBillingOrderRepository = $eventsiteBillingOrderRepository;
    }
    
    /**
     * index
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @param  mixed $attendee_id
     * @return void
     */
    public function index(Request $request, $event_url, $order_id, $attendee_id)
    {
        $request->merge(['order_id' => $order_id, 'attendee_id' => $attendee_id]);

        try {
            if ($request->isMethod('POST') && $request->action == 'save') {

                $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

                if($EBOrder->getModelAttribute('is_waitinglist') == 1 && $EBOrder->getModelAttribute('status') == 'completed') {

                    return response()->json([
                        'success' => false,
                        'message' => "You cannot update waiting list order",
                    ], $this->successStatus);

                } else {

                    $this->networkInterestRepository->saveOrderKeywords($request->all());

                    //Save request logs
                    //$EBOrder->saveOrderLogs($request->only(['keywords', 'event_id', 'attendee_id', 'order_id']));

                    return response()->json([
                        'success' => true,
                        'data' => array(
                            "order_id" => $order_id,
                            "attendee_id" => $attendee_id,
                        ),
                    ], $this->successStatus);
                }

            } else {

                $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

                $order_attendee = $EBOrder->_getAttendeeByID($attendee_id)->getOrderAttendee();

                $registration_form = $EBOrder->getRegistrationForm($attendee_id);

                $request->merge(['registration_form_id' => $registration_form ? $registration_form->id : 0]);

                $keywords = $this->networkInterestRepository->getAllKeywords($request->all());

                $attendeeKeywords = $this->networkInterestRepository->getAttendeeKeywords($request->all());

                $labels = $request->event['labels'];

                $event_id = $EBOrder->getOrderEventId();

                $language_id = $EBOrder->getUtility()->getLangaugeId();

                $payment_setting = $EBOrder->_getPaymentSetting();

                $billing_currency = $payment_setting['eventsite_currency'];

                // Order detail summary
                $order = $this->eventsiteBillingOrderRepository->getOrderDetailInvoice("json", $EBOrder, $labels, $language_id, $event_id, $billing_currency, $order_id,1, 1, true, false, 0, 0, true);

                return response()->json([
                    'success' => true,
                    'data' => array(
                        "keywords" => $keywords,
                        "attendeeKeywords" => $attendeeKeywords,
                        "order" => $order,
                        "order_attendee" => $order_attendee
                    ),
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