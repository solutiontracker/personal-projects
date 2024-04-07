<?php
namespace App\Http\Controllers\RegistrationFlow;

use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\HotelRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;
use App\Eventbuizz\Repositories\FormBuilderRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Http\Controllers\RegistrationFlow\Requests\SearchHotelRequest;
class ManageHotelController extends Controller
{
    public $successStatus = 200;

    protected $eventRepository;

    protected $hotelRepository;

    private $eventsiteBillingOrderRepository;

    /**
     * @param EventRepository $eventRepository
     * @param HotelRepository $hotelRepository
     * @param  mixed $eventsiteBillingOrderRepository
     */
    public function __construct(EventRepository $eventRepository, HotelRepository $hotelRepository, EventsiteBillingOrderRepository $eventsiteBillingOrderRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->hotelRepository = $hotelRepository;
        $this->eventsiteBillingOrderRepository = $eventsiteBillingOrderRepository;
    }

    /**
     * @param Request $request
     * @param mixed $event_url
     * @param mixed $order_id
     * @param mixed $attendee_id
     *
     * @return [type]
     */
    public function index(Request $request, $event_url, $order_id, $attendee_id)
    {
        $request->merge(["status" => 1]);

        $description = $this->hotelRepository->hotelDescription($request->all());

        $event_date_format = $this->eventRepository->getEventDateFormat($request->all());
        
        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);
        
        $order_attendee = $EBOrder->_getAttendeeByID($attendee_id)->getOrderAttendee();

        $attendee_type = $EBOrder->_getAttendeeByID($attendee_id)->getOrderAttendee()->attendee_type;
        
        $registration_form = $EBOrder->getRegistrationForm($attendee_id);
        
        $registration_form_id = $registration_form ? $registration_form->id : 0;
        
        $request->merge(['registration_form_id'=> $registration_form_id]);

        $hotel_min_date = $this->hotelRepository->hotelMinDate($request->all());

        $hotel_max_date = $this->hotelRepository->hotelMaxDate($request->all());

        //labels
        $labels = $request->event['labels'];

        $event_id = $EBOrder->getOrderEventId();

        $language_id = $EBOrder->getUtility()->getLangaugeId();

        $payment_setting = $EBOrder->_getPaymentSetting();

        $payment_form_setting = $EBOrder->_getPaymentFormSetting($registration_form_id)->toArray();

        $eventsite_form_setting = $EBOrder->_getEventsiteFormSetting($registration_form_id)->toArray();

        $billing_currency = $payment_setting['eventsite_currency'];

        // Order detail summary
        $order = $this->eventsiteBillingOrderRepository->getOrderDetailInvoice("json", $EBOrder, $labels, $language_id, $event_id, $billing_currency, $order_id,1, 1, true, false, 0, 0, true);

        //Dropdown
        $attendees = array();

        foreach($EBOrder->getAllAttendees() as $key => $attendee) //assign attendeed to event now.
        {
            if($attendee->getOrderAttendee()->attendee_type == $attendee_type) {
                $attendees[] = array(
                    'id' => $attendee->getModel()->id,
                    'value' => $attendee->getModel()->id,
                    'label' => $attendee->getModel()->first_name.' '.$attendee->getModel()->last_name
                );
            }
        }
        //End

        // Hotels
        $request->merge(['registration_form_id' => $registration_form ? $registration_form->id : 0, 'checkin'=> \Carbon\Carbon::now()->format('Y-m-d'), 'checkout'=> \Carbon\Carbon::now()->addYear()->format('Y-m-d'), 'room' => 1]);

        $hotels = $this->hotelRepository->searchHotels($request->all(), true);

        $form_builder_forms = FormBuilderRepository::getFormsStatic($request->event_id, $request->language_id, $registration_form_id);

        return response()->json([
            'success' => true,
            'data' => array(
                "order_attendee" => $order_attendee,
                "description" => $description,
                "hotels" => $hotels,
                "event_date_format" => $event_date_format,
                "hotel_min_date" => $hotel_min_date,
                "hotel_max_date" => $hotel_max_date,
                "attendees" => $attendees,
                "order" => $order,
                "registration_form_id" => $registration_form_id,
                "form_settings" => array_merge((array)$eventsite_form_setting, (array)$payment_form_setting),
                "form_builder_forms" => $form_builder_forms
            ),
        ], $this->successStatus);
    }

    /**
     * @param SearchHotelRequest $request
     * @param mixed $event_url
     * @param mixed $order_id
     * @param mixed $attendee_id
     *
     * @return [type]
     */
    public function searchHotels(SearchHotelRequest $request, $event_url, $order_id, $attendee_id)
    {
        try {
            $event = $request->event;
            
            $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

            $registration_form = $EBOrder->getRegistrationForm($attendee_id);
            
            $request->merge(['registration_form_id' => $registration_form ? $registration_form->id : 0]);

            $hotels = $this->hotelRepository->searchHotels($request->all());

            if(count($hotels) > 0) {
                return response()->json([
                    'success' => true,
                    'data' => array(
                        "hotels" => $hotels,
                        "nights" => days($request->checkin, $request->checkout)
                    ),
                ], $this->successStatus);
            } else {
                return response()->json([
                    "message" => $event['labels']['EVENTSITE_HOTEL_NO_HOTELS'],
                    "success" => false
                ], $this->successStatus);
            }

        } catch (\Exception $e) {
            return \Response::json([
                "message" => "Server error",
                "success" => false
            ]);
        }
    }

    /**
     * @param Request $request
     * @param mixed $event_url
     * @param mixed $order_id
     * @param mixed $attendee_id
     *
     * @return [type]
     */
    public function saveHotels(Request $request, $event_url, $order_id, $attendee_id)
    {
        if ($request->isMethod('POST')) {
            try { 
                $event = $request->event;

                $request->merge(["draft" => true, "action" => "add-hotel", "panel" => $request->provider ? $request->provider : "attendee"]);

                $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

                if($EBOrder->getModelAttribute('is_waitinglist') == 1 && $EBOrder->getModelAttribute('status') == 'completed') {

                    return response()->json([
                        'success' => false,
                        'message' => "You cannot update waiting list order",
                    ], $this->successStatus);

                } else {

                    $registration_form = $EBOrder->getRegistrationForm($attendee_id);

                    $registration_form_id = $registration_form ? $registration_form->id : 0;
                    
                    $payment_form_setting = $EBOrder->_getPaymentFormSetting($registration_form_id);

                    $request_data = $this->makeRequestData($request->all(), $registration_form_id, $attendee_id);

                    $attendees = (array)$this->getAllAttendees($request->all());

                    $order_persons = EventsiteBillingOrderRepository::getOrderPersons($order_id, $attendees);

                    if((count($attendees) != count(array_unique($attendees)) || $order_persons > 0) && $payment_form_setting->allow_one_person_to_one_room_only == 1) {
                        return response()->json([
                            'success' => false,
                            'message' => $event['labels']['REGISTRATION_FORM_ALLOW_TO_HOTEL_ONE_PERSON_ONLY'],
                        ], $this->successStatus);
                    }

                    $request->merge([
                        'hotel_data' => $request_data,
                        "update_hotel_state" => 1
                    ]);

                    $order = $EBOrder->updateOrder();

                    $order->save();

                    return response()->json([
                        'success' => true,
                        'message' => "Hotels save successfully!",
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

    /**
     * @param mixed $request_data
     * @param int $registration_form_id
     * @param int $attendee_id
     *
     * @return [type]
     */
    public function makeRequestData($request_data, $registration_form_id, $attendee_id)
    {
        $hotels = array();
        
        foreach ($request_data['hotels'] as $key => $input) {
            $hotel = json_decode($input, true);
            if ($hotel['checked'] == 1) {
                $hotel['link_type'] = "new";
                $hotel['registration_form_id'] = $registration_form_id;
                $hotel['attendee_id'] = $attendee_id;
                array_push($hotels, $hotel);
            }
        }

        return $hotels;
    }
        
    /**
     * getAllAttendees
     *
     * @param  mixed $request_data
     * @return void
     */
    public function getAllAttendees($request_data)
    {
        $attendees = array();
        
        foreach ($request_data['hotels'] as $key => $input) {
            $hotel = json_decode($input, true);
            if ($hotel['checked'] == 1) {
                for ($i = 1; $i <= $hotel['rooms']; $i++) {
                    $attendees[] = $hotel['hotel_person_room_'.$i];
                }
            }
        }

        return $attendees;
    }
    
    /**
     * deleteHotel
     *
     * @param  mixed $request
     * @return void
     */
    public function delete(Request $request, $event_url, $order_id
    , $order_hotel_id)
    {
        if ($request->isMethod('POST')) {

            try {
                $request->merge(["draft" => true, "action" => "remove-hotel", 'order_hotel_id' => $order_hotel_id, "panel" => $request->provider ? $request->provider : "attendee"]);

                $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

                $order = $EBOrder->updateOrder();

                $order->save();

                return response()->json([
                    'success' => true,
                    'message' => "Hotels delete successfully!",
                ], $this->successStatus);
            } catch (\Exception $e) {
                return \Response::json([
                    "message" => "Server error",
                    "success" => false
                ]);
            }
        }
    }
}
