<?php
namespace App\Http\Controllers\RegistrationFlow;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\EventsiteBillingItemRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;

class ManageItemController extends Controller
{
    public $successStatus = 200;

    protected $attendeeRepository;

    protected $eventSiteSettingRepository;

    protected $eventsiteBillingItemRepository;

    private $eventsiteBillingOrderRepository;

    /**
     * @param AttendeeRepository $attendeeRepository
     * @param EventSiteSettingRepository $eventSiteSettingRepository
     * @param EventsiteBillingItemRepository $eventsiteBillingItemRepository
     * @param  mixed $eventsiteBillingOrderRepository
     */
    public function __construct(AttendeeRepository $attendeeRepository, EventSiteSettingRepository $eventSiteSettingRepository, EventsiteBillingItemRepository $eventsiteBillingItemRepository, EventsiteBillingOrderRepository $eventsiteBillingOrderRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->eventSiteSettingRepository = $eventSiteSettingRepository;
        $this->eventsiteBillingItemRepository = $eventsiteBillingItemRepository;
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
        try {

            $setting = $request->event['eventsite_setting'];

            $payment_setting = $request->event['payment_setting'];

            $label = $request->event['labels'];

            $request->merge(["is_free" => ($setting->payment_type == 0 ? 1 : 0), "rule" => true, "draft" => true, "order_id" => $order_id, "attendee_id" => $attendee_id, "action" => "update-attendee-items", "panel" => $request->provider ? $request->provider : "attendee"]);

            $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);
            
            $order_attendee = $EBOrder->_getAttendeeByID($attendee_id)->getOrderAttendee();

            $registration_form = $EBOrder->getRegistrationForm($attendee_id);

            $request->merge(['registration_form_id' => $registration_form ? $registration_form->id : 0]);

            $items = $this->eventsiteBillingItemRepository->getRegistrationItems($request->all());

            $currencies = getCurrencyArray();

            if ($request->isMethod('POST')) {

                if($EBOrder->getModelAttribute('is_waitinglist') == 1 && $EBOrder->getModelAttribute('status') == 'completed') {

                    return response()->json([
                        'success' => false,
                        'message' => "You cannot update waiting list order",
                    ], $this->successStatus);

                } else {

                    $request_data = $request->except(['event', 'event_id', 'organizer_id', 'language_id']);

                    $validate = $this->validateItems($request_data, $EBOrder->getModelAttribute('id'), $attendee_id);

                    if($validate['status']) {

                        $attendeeData = $this->getAttendeeData($request_data);
            
                        $eventsite_form_setting = $EBOrder->_getEventsiteFormSetting($request->registration_form_id);
                        
                        if($setting->payment_type == 0 || ($setting->payment_type == 1 && (count($attendeeData) > 0 || $eventsite_form_setting->skip_items_step == 1))) {
                            $request->merge([
                                'attendee' => array(
                                    $attendee_id => $attendeeData
                                )
                            ]);
                
                            $order = $EBOrder->updateOrder();
                
                            $order->save();
                
                            return response()->json([
                                'success' => true,
                                'data' => array(
                                    "order_id" => $order_id,
                                    "attendee_id" => $attendee_id,
                                ),
                                'message' => "Items save successfully!",
                            ], $this->successStatus);
                        } else {
                            return response()->json([
                                'success' => false,
                                'message' => $label['REGISTRATION_FORM_SELECT_ITEM_TO_PROCEED'],
                            ], $this->successStatus);
                        }
                        
                    } else {
                        return response()->json($validate, $this->successStatus);
                    }
                }
            }

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
                    "orderAttendeeItemsCount" => count((array)$EBOrder->_getAttendeeByID($attendee_id)->getItems()),
                    "registrationItems" => $items['registrationItems'],
                    "currency" => $currencies[$payment_setting->eventsite_currency],
                    "order" => $order,
                    "order_attendee" => $order_attendee
                ),
            ], $this->successStatus);
            
        } catch (\Exception $e) {
            return \Response::json([
                "message" => "Server error",
                "success" => false
            ]);
        }
    }

    /**
     * @param mixed $items
     * @param mixed $formInput
     *
     * @return [type]
     */
    public function getAttendeeData($request_data)
    {
        $itemsData = array();

        $i = 0;
        
        foreach ($request_data['items'] as $item) {
            $item = json_decode($item, true);
            if ($item['type'] == 'group') {
                foreach ($item['group_data'] as $group_item) {
                    if ($group_item['is_default'] == 1) {
                        $itemsData[$i] = $group_item;
                        $i++;
                    }
                }
            } else {
                if ($item['is_default'] == 1) {
                    $itemsData[$i] = $item;
                    $i++;
                }
            }
        }

        return $itemsData;
    }
    
    /**
     * validateItems
     *
     * @param  mixed $request_data
     * @param  int $order_id
     * @param  int $attendee_id
     * @return void
     */
    public function validateItems($request_data, $order_id, $attendee_id)
    {
        $input = request()->all();

        $labels = request()->event['labels'];

        foreach ($request_data['items'] as $key => $item) {

            $item = json_decode($item, true);

            if(!$this->eventsiteBillingItemRepository->validateItem($item['id'], $input['event_id'], $input['organizer_id'])) {
                return [
                    "status" => false,
                    "message" => $item['detail']['item_name']. $labels['REGISTRATION_FORM_ITEM_NOT_EXIST']
                ];
            }
            
            if ($item['type'] == 'group') {

                //Required group validate
                $count = collect($item['group_data'])->where('is_default', 1)->count();

                if($count == 0 && $item['group_required'] == "yes") {
                    return [
                        "status" => false,
                        "message" => sprintf($labels['REGISTRATION_FORM_SELECT_ALL_REQUIRED_ITEMS'], $item['detail']['group_name'])
                    ];
                }
                //End

                //Group items
                foreach($item['group_data'] as $groupItem) {
                    
                    // Reconsider  group item stock again on post request
                    if ($groupItem['total_tickets'] > 0 || $groupItem['link_to_id'] > 0) {
                        $response = EventsiteBillingItemRepository::getItemRemainingTickets($groupItem['id'], $groupItem['total_tickets']);
                        $groupItem['remaining_tickets'] = (int)$response['remaining_tickets'];
                        $groupItem['total_tickets'] = $response['total_tickets'];
                    }

                    //Required group item validate 
                    if($groupItem['is_required'] == 1 && $groupItem['is_default'] == 0 && $groupItem['remaining_tickets'] != 0) {
                        return [
                            "status" => false,
                            "message" => sprintf("'%s' ".__('messages.field_required'), $groupItem['detail']['item_name'])
                        ];
                    }
                    //End

                    // If order is editing then add order item existing qty into stock
                    $orderItem = \App\Models\BillingOrderAddon::where('order_id', $order_id)->where('attendee_id', $attendee_id)->where('addon_id', $groupItem['id'])->first();
                    
                    if($orderItem) {
                        if($groupItem['remaining_tickets'] != 'Unlimited') { 
                            $groupItem['remaining_tickets'] = ($groupItem['remaining_tickets'] + $orderItem['qty']);
                        }
                    }

                    //Stock validate
                    if($groupItem['total_tickets'] != 0 && $groupItem['quantity'] > $groupItem['remaining_tickets'] && $groupItem['remaining_tickets'] != 'Unlimited' && $groupItem['is_default'] == 1) {
                        return [
                            "status" => false,
                            "message" => sprintf("'%s' ".$labels['REGISTRATION_FORM_ITEM_HAS_NO_REMAINING_TICKETS'], $groupItem['detail']['item_name'])
                        ];
                    }
                    //End
                }
                
            } else {

                // Reconsider item stock again on post request
                if ($item['total_tickets'] > 0 || $item['link_to_id'] > 0) {
                    $response = EventsiteBillingItemRepository::getItemRemainingTickets($item['id'], $item['total_tickets']);
                    $item['remaining_tickets'] = (int)$response['remaining_tickets'];
                    $item['total_tickets'] = $response['total_tickets'];
                }

                //Required item validate 
                if($item['is_required'] == 1 && $item['is_default'] == 0 && $item['remaining_tickets'] != 0) {
                    return [
                        "status" => false,
                        "message" => sprintf("'%s' ".__('messages.field_required'), $item['detail']['item_name'])
                    ];
                }
                //End

                // If order is editing then add order item existing qty into stock
                $orderItem = \App\Models\BillingOrderAddon::where('order_id', $order_id)->where('attendee_id', $attendee_id)->where('addon_id', $item['id'])->first();
                
                if($orderItem) {
                    if($item['remaining_tickets'] != 'Unlimited') { 
                        $item['remaining_tickets'] = ($item['remaining_tickets'] + $orderItem['qty']);
                    }
                }

                //Stock validate
                if($item['total_tickets'] != 0 && $item['quantity'] > $item['remaining_tickets'] && $item['remaining_tickets'] != 'Unlimited' && $item['is_default'] == 1) {
                    return [
                        "status" => false,
                        "message" => sprintf("'%s' ".$labels['REGISTRATION_FORM_ITEM_HAS_NO_REMAINING_TICKETS'], $item['detail']['item_name'])
                    ];
                }
                //End
                
            }
        }

        return [
            "status" => true
        ];
    }
}
