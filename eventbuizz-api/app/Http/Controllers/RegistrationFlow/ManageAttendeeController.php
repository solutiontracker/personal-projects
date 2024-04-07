<?php
namespace App\Http\Controllers\RegistrationFlow;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\EventSiteRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Eventbuizz\Repositories\GeneralRepository;
use App\Eventbuizz\Repositories\EventSettingRepository;
use App\Eventbuizz\Repositories\FormBuilderRepository;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RegistrationFlow\Requests\AttendeeRequest;
use App\Http\Controllers\RegistrationFlow\Requests\EventRegistrationCodeRequest;
use App\Http\Controllers\RegistrationFlow\Requests\GroupAttendeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;

class ManageAttendeeController extends Controller
{
    public $successStatus = 200;

    protected $attendeeRepository;

    protected $generalRepository;

    private $eventsiteBillingOrderRepository;

    protected $eventSettingRepository;

    /**
     * @param AttendeeRepository $attendeeRepository
     * @param GeneralRepository $generalRepository
     * @param EventSettingRepository $eventSettingRepository
     * @param EventsiteBillingOrderRepository $eventsiteBillingOrderRepository
     */
    public function __construct(AttendeeRepository $attendeeRepository, GeneralRepository $generalRepository, EventSettingRepository $eventSettingRepository, EventsiteBillingOrderRepository $eventsiteBillingOrderRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->generalRepository = $generalRepository;
        $this->eventSettingRepository = $eventSettingRepository;
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
    public function index(AttendeeRequest $request, $event_url, $order_id = null, $attendee_id = null)
    {
        $event = $request->event;

        $labels = $request->event['labels'];

        $eventsite_setting = $event['eventsite_setting'];

        try {
            if ($request->isMethod('POST') && !$order_id) {

                request()->merge([ "panel" => $request->provider ? $request->provider : "attendee", "draft" => true ]);

                $request_data = $this->makeRequestData($request->all());

                $createOrder = array(
                    'attendee_types' => $request->attendee_types,
                    'mainAttendee' => $request_data,
                    'is_waiting' => (int)request()->is_waiting
                );

                //Create order
                $EBOrder = new \App\Eventbuizz\EBObject\EBOrder($createOrder);

                $EBOrder->save();

                $attendee_id = $attendee_id ? $attendee_id : $EBOrder->getLastAttendee()->getModel()->id;

                return response()->json([
                    'success' => true,
                    'data' => array(
                        "order" => $EBOrder->getModel(),
                        "attendee_id" => $attendee_id,
                    ),
                ], $this->successStatus);

            } else if ($request->isMethod('POST') && $order_id) {

                $request_data = $this->makeRequestData($request->all());

                request()->merge([ "panel" => $request->provider ? $request->provider : "attendee", "action" => ($attendee_id ? "update-attendee" : "add-attendee"), "draft" => true, "attendee" => $request_data]);

                //Get order
                $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

                if($EBOrder->getModelAttribute('is_waitinglist') == 1 && $EBOrder->getModelAttribute('status') == 'completed') {

                    return response()->json([
                        'success' => false,
                        'message' => "You cannot update waiting list order",
                    ], $this->successStatus);

                } else {
                    $order = $EBOrder->updateOrder();

                    if($order) {
                        $order->save();
                        $attendee_id = $order->_getAttendeeByEmail($request_data['email'])->getModel()->id;
                    } 
                    
                    return response()->json([
                        'success' => true,
                        'data' => array(
                            "order" => $EBOrder->getModel(),
                            "attendee_id" => $attendee_id,
                        ),
                    ], $this->successStatus);
                }
                
            } else {

                $food = EventSettingRepository::getFoodAllergies($request->all());

                $disclaimer = $this->eventSettingRepository->getDisclaimer($request->event_id, $request->language_id);

                $event_calling_code = EventRepository::getEventCallingCode(['event_id' => $request->event_id]);

                $attendee = array(
                    'accept_foods_allergies' => $event['country_id'],
                    'country' => $event['country_id'],
                    'private_country' => $event['country_id'],
                    'company_country' => $event['country_id'],
                    'subscriber_ids' => [],
                    'calling_code_phone' => $event_calling_code ? $event_calling_code : "+45",
                );

                
                $metadata = $this->generalRepository->getMetadata('countries,country_codes', $request->event_id);
                
                $languages = $this->generalRepository->getMetadata('country_languages', $request->event_id);
                
                $languages = $languages['languages'];
                
                $gdpr = EventSettingRepository::getGdprInfo($request->all());
                
                if($gdpr) {
                    $purchase_policy_line_text = $gdpr->inline_text;
            
                    $gdpr->purchase_policy_link_text =  between('{detail_link}', '{/detail_link}', $purchase_policy_line_text);
            
                    $gdpr->inline_text = str_replace('{detail_link}'.$gdpr->purchase_policy_link_text.'{/detail_link}', '', $purchase_policy_line_text);
                }
                
                foreach($languages as $key => $language) {
                    $languages[$key]['id'] = $language['name'];
                }
                
                if($order_id) {

                    $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

                    $event_id = $EBOrder->getOrderEventId();

                    $language_id = $EBOrder->getUtility()->getLangaugeId();

                    $payment_setting = $EBOrder->_getPaymentSetting();

                    $billing_currency = $payment_setting['eventsite_currency'];

                    // Order detail summary
                    $order = $this->eventsiteBillingOrderRepository->getOrderDetailInvoice("json", $EBOrder, $labels, $language_id, $event_id, $billing_currency, $order_id,1, 1, true, false, 0, 0, true);

                }

                if($order_id && $attendee_id) {

                    $order_attendee = $EBOrder->_getAttendeeByID($attendee_id)->getOrderAttendee();

                    $registration_form = $EBOrder->getRegistrationForm($attendee_id);

                    $request->merge([ "registration_form_id" => $registration_form ? $registration_form->id : 0 ]);
                    
                    $custom_fields = EventSiteRepository::getCustomFields($request->all());
                    
                    $attendee = $this->attendeeRepository->getOrderAttendeeDetail($request->event_id, $order_id, $attendee_id, $custom_fields);  

                    //Still if attendee has no phone number
                    if(!$attendee['phone']) {
                        $attendee['calling_code_phone'] = $event_calling_code ? $event_calling_code : "+45";
                    }

                    $attendee['registration_form_id'] = $registration_form ? $registration_form->id : 0;

                    $attendee['attendee_type'] = $order_attendee->attendee_type;

                } else {
                    //Set default registration form [URL]
                    if($request->registration_form_id) {
                        $registration_form = (object)EventSiteSettingRepository::getRegistrationFormById(["event_id" => $request->event_id, 'id' => $request->registration_form_id]);
                        $attendee['attendee_type'] = $registration_form > 0 ? $registration_form->type_id : 0;
                        $attendee['registration_form_id'] = $registration_form > 0 ? $registration_form->id : 0;
                    } else {
                        if($order_id && $order['order_detail']['order']->attendee_types && in_array(request()->provider, ['attendee', 'embed'])) {
                            list($type_id) = explode(',', $order['order_detail']['order']->attendee_types);
                            $default_registration_form = EventSiteSettingRepository::getDefaultRegistrationFormIdByAttendeeType(['event_id' => $event['id'], 'type_id' => $type_id]);
                        } else if($request->attendee_types) {
                            list($type_id) = explode(',', $request->attendee_types);
                            $default_registration_form = EventSiteSettingRepository::getDefaultRegistrationFormIdByAttendeeType(['event_id' => $event['id'], 'type_id' => $type_id]);
                        } else if($EBOrder && $order['order_detail']['order']->is_waitinglist) {
                            $registration_form = $EBOrder->getRegistrationForm($order['order_detail']['order']->attendee_id);
                            $default_registration_form = EventSiteSettingRepository::getDefaultRegistrationFormIdByAttendeeType(['event_id' => $event['id'], 'type_id' => $registration_form->type_id]);
                        } else {
                            $default_registration_form = EventSiteSettingRepository::getDefaultRegistrationFormId(['event_id' => $event['id']]);
                        }
                        $attendee['attendee_type'] = $default_registration_form > 0 ? $default_registration_form['id'] : 0;
                        $attendee['registration_form_id'] = $default_registration_form > 0 ? $default_registration_form['registration_form_id'] : 0;
                    }

                    $request->merge([ "registration_form_id" => $attendee['registration_form_id'] ]);

                    $custom_fields = EventSiteRepository::getCustomFields($request->all());

                }

                $form_builder_forms = FormBuilderRepository::getFormsStatic($request->event_id, $request->language_id, $attendee['registration_form_id']);

                $sections = EventSiteSettingRepository::getAllSections(["event_id" => $request->event_id, "language_id" => $request->language_id, "status" => 1, 'registration_form_id' => $attendee['registration_form_id']]);

                $subscribers = EventSiteSettingRepository::getEventNewsSubscriberSetting(["event_id" => $request->event_id, "organizer_id" => $request->organizer_id]);

                $payment_form_setting = EventSiteSettingRepository::getPaymentSetting(['registration_form_id' => $attendee['registration_form_id'], "event_id" => $request->event_id])->toArray();

                $eventsite_form_setting = EventsiteRepository::getSetting(['registration_form_id' => $attendee['registration_form_id'], "event_id" => $request->event_id])->toArray();;
                
                if($order_id && $order['order_detail']['order']->attendee_types && in_array(request()->provider, ['attendee', 'embed'])) {
                    $attendee_types = $order['order_detail']['order']->attendee_types;
                    $registration_forms = array_values(array_filter($event['eventsite_registration_forms'], function($registration_form) use($attendee_types) {
                        return in_array($registration_form['id'], explode(',', $attendee_types));
                    }));
                } else {
                    $registration_forms = $event['eventsite_registration_forms'];
                }

                //Stock 
                $stock_message = '';

                $active_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  $request->event_id, 'status' => ['draft', 'completed']], false, true);

                //Validate stock
                $total = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => $attendee['registration_form_id']], true);

                $total = request()->order_id && request()->route('attendee_id') ? $total : ($total + 1);

                //Validate global stock
                $global_total = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => 0], true);
        
                $global_total = request()->order_id && request()->route('attendee_id') ? $global_total : ($global_total + 1);

                $waiting_list_setting = EventSiteSettingRepository::getWaitingListSetting(['event_id' => $request->event_id, 'registration_form_id' => $attendee['registration_form_id']]);
                
                if(!($waiting_list_setting->status == 1 || ($waiting_list_setting->after_stocked_to_waitinglist == 1 && (((int)$eventsite_form_setting['ticket_left'] > 0 && $total > (int)$eventsite_form_setting['ticket_left']) || ((int)$eventsite_setting->ticket_left > 0 && $global_total > (int)$eventsite_setting->ticket_left))))) {

                    if((((int)$eventsite_form_setting['ticket_left'] > 0 && $total > (int)$eventsite_form_setting['ticket_left']) || ((int)$eventsite_setting->ticket_left > 0 && $global_total > (int)$eventsite_setting->ticket_left))) {
                        $stock_message = $labels['REGISTER_TICKET_END'];
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'data' => array(
                        "registration_forms" => $registration_forms,
                        "sections" => $sections,
                        "custom_fields" => $custom_fields,
                        "metadata" => $metadata,
                        "attendee" => $attendee,
                        "languages" => $languages,
                        "gdpr" => $gdpr,
                        "food" => $food,
                        "disclaimer" => $disclaimer,
                        "order" => $order,
                        "order_attendee" => $order_attendee,
                        "subscribers" => $subscribers,
                        "form_settings" => array_merge((array)$eventsite_form_setting, (array)$payment_form_setting),
                        "stock_message" => $stock_message,
                        "form_builder_forms" => $form_builder_forms
                    ),
                ], $this->successStatus);
            }
        } catch (\Exception $e) {
            return \Response::json([
                "message" => $e->getMessage(),
                "success" => false
            ]);
        }
    }

    /**
     * index
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @return void
     */
    public function addAttendees(GroupAttendeeRequest $request, $event_url)
    {
        $event = $request->event;

        try {
            if($request->isMethod('PUT')) {

                $attendees = $request->attendees;

                request()->merge([ "panel" => $request->provider ? $request->provider : "attendee", "draft" => true ]);

                $createOrder = array(
                    'attendee_types' => $request->attendee_types,
                    'mainAttendee' => (array)Arr::first($attendees),
                    'is_waiting' => (int)request()->is_waiting,
                    'additional_attendees' => (array)array_values(Arr::where($attendees, function ($value, $key) {
                        return $key != 0;
                    }))
                );

                //Create order
                $EBOrder = new \App\Eventbuizz\EBObject\EBOrder($createOrder);

                $EBOrder->save();

                $attendee_id = $EBOrder->getMainAttendee()->getModel()->id;

                return response()->json([
                    'success' => true,
                    'data' => array(
                        "order" => $EBOrder->getModel(),
                        "attendee_id" => $attendee_id,
                    ),
                ], $this->successStatus);
            } else {

                $metadata = $this->generalRepository->getMetadata('countries,country_codes', $request->event_id);
        
                $attendee = array(
                    'country' => $event['country_id']
                );
        
                $totalAttendees = array();

                $attendees = array();
                
                for($i = 0; $i < 20; $i++) {
                    $totalAttendees[$i] = array(
                        'id' => $i + 1,
                        'name' => $i + 1
                    );
                }
        
                if($request->attendee_types) {
                    list($type_id) = explode(',', $request->attendee_types);
                    $default_registration_form = EventSiteSettingRepository::getDefaultRegistrationFormIdByAttendeeType(['event_id' => $event['id'], 'type_id' => $type_id]);
                } else {
                    $default_registration_form = EventSiteSettingRepository::getDefaultRegistrationFormId(['event_id' => $event['id']]);
                }

                return response()->json([
                    'success' => true,
                    'data' => array(
                        "metadata" => $metadata,
                        "attendee" => $attendee,
                        "totalAttendees" => $totalAttendees,
                        "attendees" => [array(
                            'attendee_type' => $default_registration_form > 0 ? $default_registration_form['id'] : 0
                        )]
                    ),
                ], $this->successStatus);
            }
        } catch (\Exception $e) {
            return \Response::json([
                "message" => $e->getMessage(),
                "success" => false
            ]);
        }
    }

    /**
     * @param mixed $request_data
     *
     * @return [type]
     */
    public function makeRequestData($request_data)
    {
        $custom_fields = array();

        foreach ($request_data as $key => $input) {
            if (Str::of($key)->startsWith('custom-field-')) {
                if(is_array($input) && count($input) > 0) {
                    foreach($input as $custom_field) {
                        $custom_field = json_decode($custom_field, true);
                        array_push($custom_fields, $custom_field['value']);
                    }
                    unset($request_data[$key]);
                } else if(!is_array($input)) {
                    array_push($custom_fields, $request_data[$key]);
                    unset($request_data[$key]);
                }
            } else if($request_data[$key] == "null") {
                $request_data[$key] = "";
            }
        }

        $request_data['custom_field_id'] = $custom_fields;

        $languages = array();

        foreach ($request_data['SPOKEN_LANGUAGE'] as $language) {
            $language = json_decode($language, true);
            array_push($languages, $language['value']);
        }

        $request_data['SPOKEN_LANGUAGE'] = $languages;

        $request_data['security'] = "yes";

        unset($request_data['event']);

        return $request_data;
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
    public function deleteOrderAttendee(Request $request, $event_url, $order_id, $attendee_id)
    {
        request()->merge([ "panel" => $request->provider ? $request->provider : "attendee", "action" => "delete-attendee", "draft" => true, "attendee_id" => $attendee_id]);

       //Get order
       $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);
       
       $count = count($EBOrder->getAllAttendees());

        if($count > 1) {
            $order = $EBOrder->updateOrder();
            $order->save();

            return response()->json([
                'success' => true,
                'message' => "Attendee deleted successfully!",
            ], $this->successStatus);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Please select at least one item for each attendee or add at least 1 attendee to proceed.",
            ], $this->successStatus);
        }
    }

    /**
     * autoregister
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $ids
     * @return void
     */
    public function autoregister(Request $request, $event_url, $ids)
    {
        $labels = $request->event['labels'];
        
        $request->merge([ "ids" => $ids ]);

        $event = $request->event;

        $response = $this->attendeeRepository->autoregister($request->all());

        $food = EventSettingRepository::getFoodAllergies($request->all());

        $disclaimer = $this->eventSettingRepository->getDisclaimer($request->event_id, $request->language_id);

        if(isset($response['registration_form']) && $response['registration_form']->id) {
            $registration_form_id = $response['registration_form']->id;
            $attendee_type = $response['registration_form']->type_id;
        } else {
            if($request->attendee_types) {
                list($type_id) = explode(',', $request->attendee_types);
                $default_registration_form = EventSiteSettingRepository::getDefaultRegistrationFormIdByAttendeeType(['event_id' => $event['id'], 'type_id' => $type_id]);
            } else {
                $default_registration_form = EventSiteSettingRepository::getDefaultRegistrationFormId(['event_id' => $event['id']]);
            }
            $attendee_type = $default_registration_form > 0 ? $default_registration_form['id'] : 0;
            $registration_form_id = $default_registration_form > 0 ? $default_registration_form['registration_form_id'] : 0;
        }

        $attendee = array(
            'accept_foods_allergies' => $event['country_id'],
            'country' => $event['country_id'],
            'private_country' => $event['country_id'],
            'company_country' => $event['country_id'],
            'attendee_type' => $attendee_type,
            'registration_form_id' => $registration_form_id,
        );

        if($response['success'] && isset($response['attendee']) && $response['attendee']) {
            $attendee = array_merge($attendee, $response['attendee']);
            unset($response['attendee']);
        }
        
        $request->merge([ "registration_form_id" => $registration_form_id ]);

        $custom_fields = EventSiteRepository::getCustomFields($request->all());

        $metadata = $this->generalRepository->getMetadata('countries,country_codes', $request->event_id);

        $languages = $this->generalRepository->getMetadata('languages', $request->event_id);

        $languages = $languages['languages']->toArray();

        $gdpr = EventSettingRepository::getGdprInfo($request->all());

        if($gdpr) {
            $purchase_policy_line_text = $gdpr->inline_text;
    
            $gdpr->purchase_policy_link_text =  between('{detail_link}', '{/detail_link}', $purchase_policy_line_text);
    
            $gdpr->inline_text = str_replace('{detail_link}'.$gdpr->purchase_policy_link_text.'{/detail_link}', '', $purchase_policy_line_text);
        }

        foreach($languages as $key => $language) {
            $languages[$key]['id'] = $language['name'];
        }

        $sections = EventSiteSettingRepository::getAllSections(["event_id" => $request->event_id, "language_id" => $request->language_id, "status" => 1, 'registration_form_id' => $registration_form_id]);

        $payment_form_setting = EventSiteSettingRepository::getPaymentSetting(['registration_form_id' => $attendee['registration_form_id'], "event_id" => $request->event_id])->toArray();

        $eventsite_form_setting = \App\Models\EventsiteSetting::where('event_id', '=', $request->event_id)->where('registration_form_id', $attendee['registration_form_id'])->first()->toArray();

        //Apply autoload fields 
        $attendee = AttendeeRepository::autoFillAttendeeFields($sections, $attendee);

        //Stock 
        $stock_message = '';

        $active_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  $request->event_id, 'status' => ['draft', 'completed']], false, true);

        //Validate stock
        $total = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => $attendee['registration_form_id']], true);

        $total = $total + 1;

        //Validate global stock
        $global_total = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => 0], true);

        $global_total = $global_total + 1;

        $waiting_list_setting = EventSiteSettingRepository::getWaitingListSetting(['event_id' => $request->event_id, 'registration_form_id' => $attendee['registration_form_id']]);
        
        if(!($waiting_list_setting->status == 1 || ($waiting_list_setting->after_stocked_to_waitinglist == 1 && (((int)$eventsite_form_setting['ticket_left'] > 0 && $total > (int)$eventsite_form_setting['ticket_left']) || ((int)$eventsite_setting->ticket_left > 0 && $global_total > (int)$eventsite_setting->ticket_left))))) {

            if((((int)$eventsite_form_setting['ticket_left'] > 0 && $total > (int)$eventsite_form_setting['ticket_left']) || ((int)$eventsite_setting->ticket_left > 0 && $global_total > (int)$eventsite_setting->ticket_left))) {
                $stock_message = $labels['REGISTER_TICKET_END'];
            }
        }
        
        return response()->json([
            'success' => $response['success'],
            'data' => array_merge(array(
                "sections" => $sections,
                "custom_fields" => $custom_fields,
                "metadata" => $metadata,
                "attendee" => $attendee,
                "languages" => $languages,
                "gdpr" => $gdpr,
                "food" => $food,
                "disclaimer" => $disclaimer,
                "form_settings" => array_merge((array)$eventsite_form_setting, (array)$payment_form_setting),
                "stock_message" => $stock_message
            ), $response),
        ], $this->successStatus);
        
    }

    /**
     * completeAttendeeIteration
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @return void
     */
    public function completeAttendeeIteration(Request $request, $event_url)
    {
        $request->merge(["draft" => true, "order_id" => $request->order_id, "attendee_id" => $request->attendee_id, "action" => "attendee-iteration-completed"]);

        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $request->order_id);

        $EBOrder->updateOrder();

        return response()->json([
            'success' => true
        ], $this->successStatus);
    }

    /**
     * getOrderAttendeeStatus
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @return void
     */
    public function getOrderAttendeeStatus(Request $request, $event_url, $order_id, $attendee_id)
    {
        $request->merge(["draft" => true, "order_id" => $order_id, "attendee_id" => $attendee_id]);

        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $request->order_id);

        $order_attendee = $EBOrder->_getAttendeeByID($request->attendee_id)->getOrderAttendee();

        $registration_form = $EBOrder->getRegistrationForm($attendee_id);

        $registration_form_id = $registration_form ? $registration_form->id : 0;

        $payment_form_setting = $EBOrder->_getPaymentFormSetting($registration_form_id)->toArray();
        
        $eventsite_form_setting = $EBOrder->_getEventsiteFormSetting($registration_form_id)->toArray();

        $form_builder_forms = FormBuilderRepository::getFormsStatic($request->event_id, $request->language_id, $registration_form_id);

        return response()->json([
            'success' => true,
            'data' => array(
                'order_attendee' => $order_attendee,
                'order' => $EBOrder->getModel(),
                'form_settings' => array_merge((array)$eventsite_form_setting, (array)$payment_form_setting),
                'form_builder_forms' => $form_builder_forms
            ),
        ], $this->successStatus);
    }
    
    /**
     * validateRegistrationCode
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @return void
     */
    public function validateEventRegistrationCode(EventRegistrationCodeRequest $request, $event_url)
    {
        return response()->json([
            'success' => true,
        ], $this->successStatus);
    }

}
