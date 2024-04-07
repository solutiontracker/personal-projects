<?php
namespace App\Eventbuizz\EBObject;

use App\Events\RegistrationFlow\Event;

use Carbon\Carbon;

use Illuminate\Support\Arr;

use App\Eventbuizz\Repositories\EventsiteBillingItemRepository;

use App\Eventbuizz\Repositories\EventSiteSettingRepository;

use Illuminate\Support\Str;

class EBOrder
{
    protected $_model; //App\Models\BillingOrder

    protected $_utility;

    //read only attributes
    protected $_event; //App\Models\Event

    protected $_event_info; //App\Models\EventInfo

    protected $_eventSetting = []; //App\Models\EventSetting

    protected $_eventsite_setting; //App\Models\EventSiteSetting

    protected $_eventsite_form_setting; //App\Models\EventSiteSetting

    protected $subregistration_setting; //App\Models\EventSubRegistrationSetting

    protected $_orgainzer; //App\Models\Organizer

    protected $_event_billing_items; //App\Models\BillingItem

    protected $_event_order_billing_items; //App\Models\BillingItem joined with App\Models\BillingOrderAddon

    protected $_payment_setting; //App\Models\EventsitePaymentSetting

    protected $_payment_form_setting; //App\Models\EventsitePaymentSetting

    protected $_event_billing_fields = []; //App\Models\BillingField

    protected $_orderDetail; //App\Models\BillingOrder

    protected $_mainAttendee; //App\Models\Attendee

    protected $_previous_version; // Previous version EBOrder

    protected $_is_placed; // 0 => in process, 1 =>  1 for placed

    protected $_is_view = 0; // 1 => display order with trashed data

    protected $_labels = []; //\App\Models\EventSiteText

    protected $_billing_voucher; //\App\Models\BillingVoucher

    protected $_order_attendees; //\App\Models\BillingOrderAttendee

    protected $_order_hotels; //\App\Models\EventOrderHotel

    //Writeable attributes
    protected $_items = [];
    protected $_discounts;
    protected $_VATs;

    /* @var $_VATs_detail EBOrderVATDetail */
    protected $_VATs_detail;
    protected $_vouchers;
    protected $_attendees;

    /* @var $_hotel EBOrderHotel */
    protected $_hotel;

    protected $_edit_flag = 0;

    protected $_generated_tickets_ids = [];

    //This is only set immediately after order placement, so that we can use it in emails. Otherwise it will always return null even if order has tickets
    protected $_tickets_pdf_file;

    protected $_removed_attendees = [];

    protected $_removed_attendee_items = [];

    protected $_items_voucher_usage_limits; //Used for billing item voucher only.

    protected $_invoice_summary = [];

    protected $_is_deleted_main_attendee = 0;

    protected $_removed_hotels = [];

    public function __construct($data = array(), $order_id = null, $_is_view = false)
    {
        $this->_utility = new EBUtility($data, request()->panel, request()->organizer_id, request()->event_id, request()->language_id, request()->draft, request()->sale_id);

        //Settings 
        $this->_getEventSiteSetting();
        $this->_getPaymentSetting();

        if (!is_null($order_id)) {
            $this->_model = \App\Models\BillingOrder::findOrFail($order_id);
            $this->_event = $this->_model->event;
            set_event_timezone($this->getOrderEventId());
            $this->_setStatePlaced(); //set order as placed
            if ($_is_view) {$this->_setIsView();}

        } else {
            $this->_setStateInProgress(); //set order as in progress
            $this->_model = new \App\Models\BillingOrder();
            $this->_event = $this->_getEvent();
            set_event_timezone($this->getOrderEventId());
            $this->applyVoucher();
        }

        $this->_event_info = $this->_getEventInfo();

        //Fetch event setting
        $this->_eventSetting = $this->_getEventSetting();

        //Fetch organizer
        $this->_orgainzer = $this->_getOrganizer();

        $this->_setupItemVoucherLImits();
        $this->_setAttendees(); //Rely on getData() directly
        $this->_setItems(); //Rely on getData() directly
        $this->_setupOrderDiscounts(); //Rely on getData() directly
        $this->_setHotel(); //Rely on getData() directly
        $this->_setVATs(); //Rely on getData() directly
        $this->_checkintegrity(true);
    }

    public function _setStateInProgress()
    {
        $this->_is_placed = 0;
    }

    private function _setStatePlaced()
    {
        $this->_is_placed = 1;
    }

    private function _setIsView()
    {
        $this->_is_view = 1;
    }

    public function _getEvent()
    {
        if (!$this->_event) {
            $event_id = $this->_utility->getEventId();
            $this->_event = \App\Models\Event::findOrFail($event_id);
        }

        return $this->_event;
    }

    public function _getEventInfo()
    {
        if (!$this->_event_info) {
            $event_id = $this->_utility->getEventId();
            $info = \App\Models\EventInfo::where('event_id', $event_id)->get()->toArray();
            $this->_event_info = readArrayKey(['info' => $info], [], 'info');
        }

        return $this->_event_info;
    }

    public function _getEventBillingItems()
    {
        if (empty($this->_event_billing_items)) {
            $event_id = $this->_utility->getEventId();
            $this->_event_billing_items = \App\Models\BillingItem::where('event_id', '=', $event_id)->with(['Info' => function ($query) {
                return $query->where('languages_id', $this->_utility->getLangaugeId());
            }])->get();
        }

        return $this->_event_billing_items;
    }

    public function _getEventOrderBillingItems()
    {
        if (empty($this->_event_order_billing_items)) {
            $this->_event_order_billing_items = \App\Models\BillingOrderAddon::join('conf_billing_items', function ($join) {
                $join->on('conf_billing_items.id', '=', 'conf_billing_order_addons.addon_id');
            })
            ->with(['info'])
            ->where("conf_billing_order_addons.order_id", $this->getModelAttribute('id'))
            ->whereNull("conf_billing_order_addons.deleted_at")
            ->select('conf_billing_order_addons.qty as db_qty', 'conf_billing_order_addons.price as order_item_price', 'conf_billing_items.*')
            ->get()
            ->toArray();
        }

        return $this->_event_order_billing_items;
    }

    public function _getOrderDetail()
    {
        if (empty($this->_orderDetail) && $this->getModelAttribute('id')) {
            $this->_orderDetail = \App\Models\BillingOrder::where('id', $this->getModelAttribute('id'))->first();
        }

        return $this->_orderDetail;
    }

    public function _getMainAttendeeDetail()
    {
        if (empty($this->_mainAttendee) && $this->getModelAttribute('id')) {
            $this->_mainAttendee = \App\Models\Attendee::where('id', '=', $this->_model->attendee_id)->with(['info'=> function($query) {
                return $query->where('languages_id','=', $this->getUtility()->getLangaugeId());
            }])->first()->toArray();
        }

        return $this->_mainAttendee;
    }

    public function _getPaymentSetting()
    {
        if (empty($this->_payment_setting)) {
            $event_id = $this->_utility->getEventId();
            $this->_payment_setting = \App\Models\EventsitePaymentSetting::where('event_id', '=', $event_id)->where('registration_form_id', 0)->first();
        }

        return $this->_payment_setting;
    }

    public function getPaymentSettingAttribute($key)
    {
        return $this->_payment_setting->{$key};
    }
    
    public function _getPaymentFormSetting($registration_form_id, $reload = false)
    {
        $event_id = $this->_utility->getEventId();

        $this->_payment_form_setting = \App\Models\EventsitePaymentSetting::where('event_id', '=', $event_id)->where('registration_form_id', $registration_form_id)->first();

        return $this->_payment_form_setting;
    }

    public function getPaymentFormSettingAttribute($key)
    {
        return $this->_payment_form_setting->{$key};
    }

    public function _getEventSiteSetting()
    {
        if (empty($this->_eventsite_setting)) {
            $event_id = $this->_utility->getEventId();
            $this->_eventsite_setting = \App\Models\EventsiteSetting::where('event_id', '=', $event_id)->where('registration_form_id', 0)->first();
        }

        return $this->_eventsite_setting;
    }

    public function getEventsiteSettingAttribute($key)
    {
        return $this->_eventsite_setting->{$key};
    }

    public function _getEventSiteFormSetting($registration_form_id)
    {
        $event_id = $this->_utility->getEventId();
        
        $this->_eventsite_form_setting = \App\Models\EventsiteSetting::where('event_id', '=', $event_id)->where('registration_form_id', $registration_form_id)->first();

        return $this->_eventsite_form_setting;
    }

    public function getEventsiteFormSettingsAttribute($key)
    {
        return $this->_eventsite_form_setting->{$key};
    }

    public function getRegistrationForm($attendee_id)
    {
        $event_id = $this->_utility->getEventId();
        $order_attendee = $this->_getAttendeeByID($attendee_id)->getOrderAttendee();
        $registration_form = (object)EventSiteSettingRepository::getRegistrationForm(["event_id" => $event_id, 'type_id' => $order_attendee->attendee_type]);
        return $registration_form;
    }

    public function getRegistrationForms()
    {
        $registration_forms = array();

        foreach ($this->getAllAttendees() as $attendee) {
            $order_attendee = $attendee->getOrderAttendee();
            $registration_forms[] = $order_attendee->registration_form_id;
        }

        return $registration_forms;
    }

    public function _getEventSubregistrationSetting()
    {
        if (empty($this->subregistration_setting)) {
            $event_id = $this->_utility->getEventId();
            $this->subregistration_setting = \App\Models\EventSubRegistrationSetting::where('event_id', '=', $event_id)->first();
        }

        return $this->subregistration_setting;
    }

    public function _getEventLabels()
    {
        if (empty($this->_labels)) {
            $this->_labels = eventsite_labels('eventsite', ["event_id" => $this->_utility->getEventId(), "language_id" => $this->getUtility()->getLangaugeId()]);
        }

        return $this->_labels;
    }

    public function _getVoucher()
    {
        if (empty($this->_billing_voucher)) {
            $this->_billing_voucher = \App\Models\BillingVoucher::where('event_id', '=', $this->getOrderEventId())
            ->where('id', '=', $this->getModelAttribute('coupon_id'))
            ->first();
        }

        return $this->_billing_voucher;
    }

    public function _getOrderAttendees()
    {
        if (empty($this->_order_attendees)) {
            $event_id = $this->_utility->getEventId();
            $this->_order_attendees = \App\Models\BillingOrderAttendee::join('conf_attendees', function ($join) {
                $join->on('conf_billing_order_attendees.attendee_id', '=', 'conf_attendees.id');
            })
            ->where("conf_billing_order_attendees.order_id", $this->getModelAttribute('id'))
            ->get();
        }

        return $this->_order_attendees;
    }

    public function _getOrderHotels()
    {
        if (empty($this->_order_hotels)) {
            $this->_order_hotels = \App\Models\EventOrderHotel::where('order_id', $this->getModelAttribute('id'))->get();
        }

        return $this->_order_hotels;
    }

    public function _getEventbillingFields()
    {
        if (empty($this->_event_billing_fields)) {
            $event_id = $this->_utility->getEventId();
            $sections = \App\Models\BillingField::where('type','=','section')->where('event_id','=',$event_id)
                ->where('status','=','1')->with(['info'=>function($query) {
                    return $query->where('languages_id','=',$this->getUtility()->getLangaugeId());
                }])
                ->orderBy('sort_order','ASC')
                ->get()
                ->toArray();
            $sections = returnArrayKeys($sections, ['info']);
            
            foreach ($sections as $row) {
    
                $fields = \App\Models\BillingField::where('type','=','field')
                    ->where('event_id','=',$event_id)
                    ->where('section_alias','=',$row['field_alias'])
                    ->where('status','=','1')
                    ->with(['info'=>function($query) {
                        return $query->where('languages_id','=',$this->getUtility()->getLangaugeId());
                    }])
                    ->orderBy('sort_order','ASC')
                    ->get()
                    ->toArray();
                $fields = returnArrayKeys($fields, ['info']);
                foreach ($fields as $rs_fields) {
                    $this->_event_billing_fields[$row['field_alias']]		= $rs_fields['name'];
                }
            }
        }

        return $this->_event_billing_fields;
    }
    
    public function _getOrganizer()
    {
        if (empty($this->_orgainzer)) {
            $id = $this->_utility->getOrganizerID();
            $this->_orgainzer = \App\Models\Organizer::where("id", $id)->first();
        }

        return $this->_orgainzer;
    }

    public function _getEventSetting()
    {
        if (empty($this->_eventSetting)) {
            $event_id = $this->_utility->getEventId();
            $settings = \App\Models\EventSetting::where("event_id", $event_id)->get();
            if (count($settings) > 0) {
                foreach ($settings as $val) {
                    if ($val->value) {
                        $this->_eventSetting[$val->name] = $val->value;
                    }
                }
            }
        }

        return $this->_eventSetting;
    }

    public function getItemsTotal()
    {
        $sum = 0;
        foreach ($this->getAllItems() as $item) {
            // EBOrderItem
            $sum += $item->getPriceSubtotalWithDiscount();
        }

        return round(($sum - $this->getDiscountsTotalAmount()), 2);
    }

    public function getItemsSubTotal()
    {
        $sum = 0;
        foreach ($this->getAllItems() as $item) {
            // EBOrderItem
            $sum += $item->getPriceSubtotal();
        }

        return round($sum, 2);
    }

    private function _setItems()
    {
        foreach ($this->getAttendees() as $attendee) {
            // EBOrderAttendee
            foreach ($attendee->getItems() as $item) {
                // EBOrderItem
                if (isset($this->_items[$item->getBillingItemId()]) == false) {
                    $this->_items[$item->getBillingItemId()] = $item->cumulate();
                }
            }
        }
    }

    private function _setAttendees()
    {
        if ($this->isOrderPlaced()) {
            $order_attendees = $this->_model->order_attendees()->get();
            foreach ($order_attendees as $attendee) {
                $this->_attendees[] = new EBOrderAttendee($this, $attendee->attendee_id);
            }
        } else {
            if (in_array($this->getUtility()->getPanel(), ['attendee', 'embed', 'sale'])) {
                $mainAttendee = $this->getUtility()->getData('mainAttendee');
                $addons = $this->getUtility()->getData('addons');
                $additional_attendees = $this->getUtility()->getData('additional_attendees');
                $this->_attendees[] = new EBOrderAttendee($this, $mainAttendee, $addons, true);
                if (count((array) $additional_attendees) > 0 && $this->getPaymentSettingAttribute('evensite_additional_attendee') == 1) {
                    foreach ($additional_attendees as $attendee) {
                        $this->_attendees[] = new EBOrderAttendee($this, $attendee, $attendee['addons']);
                    }
                }
            } elseif ($this->getUtility()->getPanel() == 'admin') {
                throw new \Exception('Not allowed.');
            }
        }
    }

    private function _isHotelAddedEditOrder()
    {
        return request()->update_hotel_state ? true : false;
    }

    private function _setHotel()
    {
        if ($this->isOrderPlaced()) {
            if ($this->isOrderView()) {
                $hotels = $this->_model->order_hotels()->withTrashed()->get();
            } else {
                $hotels = $this->_model->order_hotels()->get();
            }
            foreach ($hotels as $hotel) {
                if ($hotel instanceof \App\Models\EventOrderHotel) {
                    // We have to use first because relationship is defined as hasmany in billingorder model with orderhotels.
                    $this->_hotel[] = new EBOrderHotel($this, $hotel);
                }
            }
        } else if ($this->isEdit() && $this->_isHotelAddedEditOrder()) {

            // Hotel added during edit order
            $hotels = request()->hotel_data;

            if (count($hotels) > 0) {
                foreach ($hotels as $hotel) {
                    if (isset($hotel['id'])) {
                        $this->_hotel[] = new EBOrderHotel($this, $hotel);
                    }
                }
            }
        } else {
            $data = $this->getUtility()->getData('hotel');
            if (isset($data['selected_hotel_rooms']) && count($data['selected_hotel_rooms']) > 0) {
                foreach ($data['selected_hotel_rooms'] as $selected_hotel) {
                    $this->_hotel[] = new EBOrderHotel($this, $selected_hotel);
                }
            }
        }
    }

    public function removeHotelsNotAttachedWithAttendees()
    {
        foreach ($this->getHotel() as $key => $hotel) {

            //Persons
            $hotel_persons = $hotel->getPersons();

            if(is_object($hotel_persons) && $hotel_persons) {
                $person_ids = array();
                foreach($hotel_persons as $hotel_person) {
                    if(!in_array($hotel_person->attendee_id, $this->_removed_attendees)) {
                        array_push($person_ids, $hotel_person);
                    }
                }
                $hotel->updateRoomPersons( $person_ids);
            } else {
                $person_ids = array_diff($hotel_persons, $this->_removed_attendees);
                $hotel->updateRoomPersons( $person_ids);
            }

            $hotel->updateRooms(count($person_ids));
            
            if(count($person_ids) == 0) {
                $this->_removed_hotels[] = $hotel->getModel()->id;
                // Remove this hotel
                unset($this->_hotel[$key]);
            }

        }
    }
    
    private function _setVATs()
    {
        if (count((array)$this->_hotel) > 0 && (($this->isVatApplicable() && $this->isOrderVatFree()) || ($this->getPaymentSettingAttribute('hotel_vat_status') == 1 && $this->isFree()))) {
            $this->_VATs[] = new EBOrderVAT($this, 'hotel');
        }

        if ($this->isVatApplicable() && $this->isOrderVatFree()) {
            $this->_VATs[] = new EBOrderVAT($this, 'items');
        }

        if ($this->getPaymentSettingAttribute('eventsite_apply_multi_vat') || ($this->isOrderPlaced() && $this->getModelAttribute('item_level_vat') == 1)) {
            $this->_VATs_detail = new EBOrderVATDetail($this);
        }
    }

    public function getOrderEventId()
    {
        return $this->_event->id;
    }

    public function getItemTotalQuantity($item_id)
    {
        // Returns items selected quantity including all attendees items selection for the order
        $quantity_for_item = 0;
        if (array_key_exists($item_id, $this->_items)) {
            // EBOrderItem
            $item = $this->_items[$item_id];
            $quantity_for_item = $item->getQuantity();
        }
        return $quantity_for_item;
    }

    public function getAllItems()
    {
        return $this->_items;
    }

    public function isOrderPlaced()
    {
        return ($this->_is_placed === 1);
    }

    public function isOrderView()
    {
        return ($this->_is_view === 1);
    }

    public function getModelAttribute($name)
    {
        if (!$name) {
            return null;
        }

        return $this->_model->{$name};
    }

    public function getUtility()
    {
        return $this->_utility;
    }

    public function isVoucherApplied()
    {
        return ($this->getModelAttribute('code') != '' && $this->getModelAttribute('coupon_id') != '');
    }

    public function getAppliedVoucherAffectedItemIds()
    {
        if ($this->getAppliedVoucherType() != 'billing_items') {
            return;
        }

        if ($this->isOrderPlaced()) {
            $voucher = \App\Models\BillingVoucher::find($this->getModelAttribute('coupon_id'));
        } else {
            $voucher = \App\Models\BillingVoucher::where(function ($q) {
                $q->where('event_id', '=', $this->getOrderEventId())->where('id', '=', $this->getModelAttribute('coupon_id'))->where('status', '=', '1');
            })->where(function ($q) {
                $q->where('expiry_date', '=', '0000-00-00')->orWhere(function ($query) {
                    $query->whereDate('expiry_date', '>=', \Carbon\Carbon::now()->format('Y-m-d'));
                });
            })->first();
        }

        if (!$voucher instanceof \App\Models\BillingVoucher) {
            throw new \Exception('Invalid voucher applied to the order');
        }

        $info = $voucher->items()->select('item_id')->get()->toArray();

        return Arr::flatten($info);
    }

    public function getAppliedVoucherType()
    {
        // Returns voucher type either billing_items / order
        if (!$this->isVoucherApplied()) {
            throw new \Exception('No voucher has been applied to the order');
        }

        if ($this->isOrderPlaced()) {
            $voucher = \App\Models\BillingVoucher::find($this->getModelAttribute('coupon_id'));
        } else {
            $voucher = \App\Models\BillingVoucher::where(function ($q) {
                $q->where('event_id', '=', $this->getOrderEventId())->where('id', '=', $this->getModelAttribute('coupon_id'))->where('status', '=', '1');
            })->where(function ($q) {
                $q->where('expiry_date', '=', '0000-00-00')->orWhere(function ($query) {
                    $query->whereDate('expiry_date', '>=', \Carbon\Carbon::now()->format('Y-m-d'));
                });
            })->first();
        }

        if (!$voucher instanceof \App\Models\BillingVoucher) {
            throw new \Exception('Invalid voucher applied to the order');
        }

        return $voucher->type;
    }

    private function applyVoucher()
    {
        //Validate voucher before apply
        if ($this->isEdit()) {
            $code = $this->_utility->getData('voucher_code');
            if ($code != '') {
                $voucher = \App\Models\BillingVoucher::where('event_id', $this->getOrderEventId())->where('code', $code)->where('status', '1')->first();
                if ($voucher instanceof \App\Models\BillingVoucher) {
                    $voucherIsValid = true;
                    if (($voucher->type == 'order' || $voucher->type == 'vat_free') && $voucher->usage > 0 && $this->getModelAttribute('coupon_id') != $voucher->id) {
                        $usedInOrdersCount = \App\Models\BillingOrder::where('code', $voucher->code)->where('event_id', $this->getOrderEventId())->where('is_voucher', '1')->where('is_archive', '0')->currentOrder()->get()->count();
                        if ($usedInOrdersCount >= $voucher->usage) {
                            $voucherIsValid = false;
                        }
                    }
                    if ($voucherIsValid) {
                        $coupon_id = $voucher->id;
                        $this->_model->code = $code;
                        $this->_model->coupon_id = $coupon_id;
                        $this->_model->is_voucher = 1;

                    } else {
                        $this->_model->code = '';
                        $this->_model->coupon_id = '';
                        $this->_model->is_voucher = 0;
                    }
                }
            } else {
                $this->_model->code = '';
                $this->_model->coupon_id = '';
                $this->_model->is_voucher = 0;
            }
        } else {
            $coupon = $this->_utility->getData('coupon');
            if ($coupon['code'] != '') {
                $this->_model->code = $coupon['code'];
                $this->_model->coupon_id = $coupon['id'];
                $this->_model->is_voucher = 1;
            }
        }
    }
    
    private function removeVoucher()
    {
        if($this->_model) {
            $this->_model->code = '';
            $this->_model->coupon_id = '';
            $this->_model->is_voucher = 0;
        }
    }

    private function _setupItemVoucherLImits()
    {
        if ($this->isVoucherApplied() && $this->getAppliedVoucherType() == 'billing_items') {
            if ($this->isOrderPlaced()) {
                $voucher = \App\Models\BillingVoucher::find($this->getModelAttribute('coupon_id'));
            } else {
                $voucher = \App\Models\BillingVoucher::where(function ($q) {
                    $q->where('event_id', $this->getOrderEventId())
                        ->where('id', $this->getModelAttribute('coupon_id'))
                        ->where('status', '1');
                })->where(function ($q) {
                    $q->where('expiry_date', '0000-00-00')
                        ->orWhere(function ($query) {
                            $query->whereDate('expiry_date', '>=', \Carbon\Carbon::now()->format('Y-m-d'));
                        });
                })->first();
            }

            $this->_items_voucher_usage_limits = $this->getUsageForItemsVoucher($voucher->id); //sets up $_items_voucher_usage_limits
        }
    }

    public function getUsageForItemsVoucher($voucher_id)
    {
        // Takes voucher id and returns array with usage limits and total used so far.
        $voucher = \App\Models\BillingVoucher::find($voucher_id);
        if (!$voucher instanceof \App\Models\BillingVoucher) {
            return false;
        }

        $items = $voucher->items()->get();

        $usages = [];

        foreach ($items as $item) {

            // 0 means no limit so only set if greater than 0
            $usage_limit = $item->useage > 0 ? $item->useage : null;

            $valid_order_ids = \App\Models\BillingOrder::where('coupon_id', '=', $item['voucher_id'])
                ->where('event_id', $this->getOrderEventId())
                ->where('is_archive', '0')
                ->currentOrder()
                ->pluck('id');

            $usedInOrdersCount = \App\Models\BillingOrderAddon::whereIn('order_id', $valid_order_ids)->where('addon_id', $item['item_id'])->sum('discount_qty');

            $usages[$item->item_id] = ['limit' => $usage_limit, 'used' => $usedInOrdersCount];
        }

        return $usages;
    }

    public function getItemVoucherUsageLimits()
    {
        return $this->_items_voucher_usage_limits;
    }

    public function updateUsedCountForItemsVoucher($item_id, $qty = 1)
    {
        if (isset($this->_items_voucher_usage_limits[$item_id])) {
            $this->_items_voucher_usage_limits[$item_id]['used'] = $this->_items_voucher_usage_limits[$item_id]['used'] + (int) $qty;
            return true;
        }
        return false;
    }

    public function updateLimitCountForItemsVoucher($item_id, $qty = 1)
    {
        if (isset($this->_items_voucher_usage_limits[$item_id])) {
            $this->_items_voucher_usage_limits[$item_id]['limit'] = $this->_items_voucher_usage_limits[$item_id]['limit'] - (int) $qty;
            return true;
        }

        return false;
    }

    public function getDiscounts()
    {
        return $this->_discounts;
    }

    public function getDiscountsTotalAmount()
    {
        $total = 0;
        foreach ($this->getDiscounts() as $discount) {
            // EBOrderDiscount
            $total = $total + $discount->getDiscountAmount();
        }
        return round($total, 2);
    }

    private function _setupOrderDiscounts()
    {
        if ($this->getPaymentSettingAttribute('use_qty_rules') == 1) {
            foreach ($this->_items as $item) {
                // EBOrderItem
                if ($item->isQuantityDiscountApplied()) {
                    $this->_discounts[] = new EBOrderDiscount($this, 'quantity', $item);
                }
            }
        }

        if ($this->isVoucherApplied() && $this->getAppliedVoucherType() == 'order') {
            $this->_discounts[] = new EBOrderDiscount($this, 'voucher');
        }
    }

    private function _checkintegrity($initial = false)
    {
        // Verify integrity of order
        if ($initial) {
            if (!($this->_event instanceof \App\Models\Event)) {
                throw new \Exception('Event id could not be found.');
            }
        }
    }

    public function isVatApplicable()
    {
        if ($this->getPaymentSettingAttribute('eventsite_always_apply_vat') == 1) {
            return true;
        } elseif ($this->getPaymentSettingAttribute('eventsite_vat_countries') != '') {
            $countries_arr = explode(',', $this->getPaymentSettingAttribute('eventsite_vat_countries'));
            if ($this->isEdit() || $this->isOrderPlaced()) {
                $billing_company_country = $this->getMainAttendee()->getBillingModelAttribute('billing_company_country');
                if ($billing_company_country != '' && in_array($billing_company_country, $countries_arr)) {
                    return true;
                }
            } else {
                $company_country = $this->_utility->getData('mainAttendee');
                if ($company_country['company_country'] != '' && in_array($company_country['company_country'], $countries_arr)) {
                    return true;
                }
            }
            return false;
        } else {
            return false;
        }
    }

    public function isOrderVatFree()
    {
        if ($this->isVoucherApplied() && $this->getAppliedVoucherType() == 'vat_free') {
            return false;
        }

        return true;
    }

    public function getVatPercentage()
    {
        return $this->getPaymentSettingAttribute('eventsite_vat');
    }

    public function isHotelVatEnabledForFreeOrder()
    {
        return ($this->getPaymentSettingAttribute('hotel_vat_status') == 1);
    }

    public function getHotelSubTotal()
    {
        $sub_total = 0;
        foreach ($this->getHotel() as $hotel) {
            if ($hotel instanceof EBOrderHotel) {
                $sub_total += $hotel->getHotelSubtotal();
            }
        }

        return $sub_total;
    }

    public function getEvent()
    {
        return $this->_event;
    }

    public function getHotel()
    {
        return $this->_hotel;
    }

    public function getVATs()
    {
        return $this->_VATs;
    }

    public function getVATsDetail()
    {
        return $this->_VATs_detail;
    }
    
    /**
     * displayVATsDetails
     * Display vats (Order summary etc)
     * @param  mixed $eventsite_currency
     * @return void
     */
    public function displayVATsDetails() {
        $currencies = getCurrencyArray();
        $eventsite_currency = $this->getPaymentSettingAttribute('eventsite_currency');
        $vats = array();
        foreach($this->_VATs_detail->getVAT() as $key=> $vat) {
            $vats[$key] = getCurrency($vat, $currencies[$eventsite_currency]). ' ' . $currencies[$eventsite_currency];
        }
        return $vats;
    }

    public function getNewSubscriberSettingsAttribute($key)
    {
        return $this->_event->eventsiteNewsSubscriberSettings[0][$key];
    }

    public function getAttendeeSettings()
    {
        if (!$this->_event->attendee_settings instanceof \App\Models\AttendeeSetting) {
            $this->_event->load('attendee_settings');
        }
        return $this->_event->attendee_settings;
    }

    public function getAttendeeSettingsAttribute($key)
    {
        $settings = $this->getAttendeeSettings();
        return $settings[$key];
    }

    public function isEdit()
    {
        // Wrapper for _isEdit private method
        return $this->_isEdit();
    }

    private function _isEdit()
    {
        // Returns true if current order is in process of edit.
        return ($this->_edit_flag == 1);
    }

    public function setIsEdit()
    {
        $this->_edit_flag = 1;
    }

    public function isFree()
    {
        return ($this->getEventsiteSettingAttribute('payment_type') == 0);
    }

    private function _saveAttendees()
    {
        foreach ($this->getAllAttendees() as $attendee) {
            
            //Remove attendee items if updated (During order process)
            if(!empty($this->_removed_attendee_items[$attendee->getModel()->id])) {
                \App\Models\BillingOrderAddon::where('order_id', $this->_model->id)->where('attendee_id', $attendee->getModel()->id)->whereIn('addon_id', $this->_removed_attendee_items[$attendee->getModel()->id])->delete();
            }

            // EBOrderAttendee
            $attendee->save();
        }

        // Important need to check if main attendee was removed. If so, then set it right after saving attendees here.
        // So that the events that are firing right after _saveAttendees() would be able to work correctly (because they assume main attendee flag is set on 1 of the attendees)
        if ($this->_isDeletedMainAttendee()) {
            $newMainAttendee = array_slice($this->_attendees, 0, 1);
            if(count($newMainAttendee) > 0) {
                $newMainAttendee[0]->updateAttendeeBillingWithNewMainAttendee();
            }
        }

        //Remove attendee from order
        foreach ($this->_removed_attendees as $attID) {
            \App\Models\BillingOrderAttendee::where('attendee_id', $attID)->where('order_id', $this->_model->id)->delete();

        }
    }

    public function getOrderAttendeesIds($order) {
        $ids = array();
        foreach ($order->getAttendees() as $attendee) {
            $ids[] = $attendee->getModel()->id;
        }
        return $ids;
    }

    public function getActiveOrderAttendees() {
        
        $order_id = $this->getModel()->clone_of;

        $event_id = $this->getOrderEventId();

        return \App\Models\BillingOrderAttendee::where('order_id', $order_id)->pluck('attendee_id')->toArray();
    }

    private function _assignEventAttendees()
    {
        if(in_array($this->getUtility()->getPanel(), ['sale', 'admin']) && !$this->getUtility()->isDraft() && $this->getPreviousVersion() instanceof \App\Eventbuizz\EBObject\EBOrder) {
            
            $current_attendee_ids = $this->getOrderAttendeesIds($this);
            
            $previous_attendee_ids = $this->getOrderAttendeesIds($this->getPreviousVersion());

            $remove_attendees = array_diff($previous_attendee_ids, $current_attendee_ids);

            //Unassign attendees
            foreach ($remove_attendees as $attID) {

                $event_attendee = \App\Models\EventAttendee::where('attendee_id', $attID)
                    ->where('event_id', $this->getOrderEventId())
                    ->whereNull('deleted_at')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($event_attendee instanceof \App\Models\EventAttendee) {

                    $event_attendee->delete(); // un-assign from event

                    //Remove attendees from groups
                    $event_groups = \App\Models\EventGroup::where('event_id', $this->getOrderEventId())
                        ->pluck('id')
                        ->toArray();

                    \App\Models\EventAttendeeGroup::whereIn('group_id', $event_groups)->where('attendee_id', $attID)->delete();
                    
                }
            }
            
        }
    }

    private function _saveHotel()
    {
        //Delete hotel during edit order
        if(count($this->_removed_hotels) > 0) {
            foreach ($this->_removed_hotels as $order_hotel_id) {
                \App\Models\EventOrderHotel::where('id', $order_hotel_id)->where('order_id', $this->getModelAttribute('id'))->delete();
                \App\Models\EventHotelPerson::where('order_id', $this->getModelAttribute('id'))->where('order_hotel_id', $order_hotel_id)->delete();
                \App\Models\EventOrderHotelRoom::where('order_id', $this->getModelAttribute('id'))->where('order_hotel_id', $order_hotel_id)->delete();
            }
        }
        //End

        foreach ($this->getHotel() as $hotel) {
            if ($hotel instanceof EBOrderHotel) {
                $hotel->save();
            }
        }
    }

    private function _saveVAT()
    {
        if ($this->getGrandTotalWithoutVAT() > 0) {
            if ($this->getVATsDetail()) {
                $vat_detail = $this->getVATsDetail();
                if ($vat_detail instanceof EBOrderVATDetail) {
                    $vat_detail->save();
                }
            }
        }
    }

    private function _wasMainAttendeeRemoved()
    {
        foreach ($this->getAllAttendees() as $attendee) {
            if ($attendee->isMainAttendee()) {
                return false;
            }
        }

        return true;
    }

    private function _isDeletedMainAttendee()
    {
        return $this->_is_deleted_main_attendee;
    }

    private function _setAsDeletedMainAttendee()
    {
        if ($this->isEdit()) {
            $this->_is_deleted_main_attendee = true;
        }
    }

    private function _updateAfterSave()
    {
        $fields = array(
            'attendee_id' => $this->getMainAttendee()->getModel()->id, // order attendee
            'comments' => '',
            'is_payment_received' => ($this->_model->is_free == 1 || $this->_model->grand_total == 0) ? 1 : $this->_model->is_payment_received,
            'payment_received_date' => ($this->_model->is_free == 1 || $this->_model->grand_total == 0) ? Carbon::now() : $this->_model->payment_received_date, 
            'is_added_reporting' => 0,
            'to_be_fetched' => 0,
            'status' => 'completed', 
        );

        $this->_model->update($fields);
    }

    private function _updateDraftOrder()
    {
        $fields = array(
            'attendee_id' => $this->getMainAttendee()->getModel()->id, // order attendee
            'status' => 'draft', 
        );

        $this->_model->update($fields);
    }

    private function _saveQtyDiscountRuleLog()
    {
        foreach ($this->getDiscounts() as $discount) {
            // EBOrderDiscount
            if ($discount->getDiscountType() == 'quantity') {
                $discount->getDiscountObject()->saveQtyRuleLog();
            }
        }
    }

    public function getMainAttendee()
    {
        // EBOrderAttendee
        foreach ($this->_attendees as $attendee) {
            if ($attendee->isMainAttendee()) {
                return $attendee;
            }
        }

        return null;
    }

    public function getLastAttendee()
    {
        $total = count($this->_attendees);
        return $this->_attendees[$total - 1];
    }

    public function isEmailValidForSystem()
    {
        return $this->_isEmailValidForSystem();
    }

    public function setNewMainAttendeeIfRemoved()
    {
        //if main attendee deleted set new main attendee from existing attendees
        if ($this->_wasMainAttendeeRemoved()) {
            $this->_setAsDeletedMainAttendee(); 
            $newMainAttendee = array_slice($this->_attendees, 0, 1);
            if(count($newMainAttendee) > 0) {
                $newMainAttendee[0]->setAsMainAttendee();
            }
        }
    }

    private function _isEmailValidForSystem()
    {
        // Checks if attendee email is unique per organizer
        $mainAttendee = $this->getUtility()->getData('mainAttendee');

        $email = $mainAttendee['email'];

        $organizer_id = $this->getUtility()->getOrganizerID();

        $attendee = \App\Models\Attendee::where('email', $email)->where('organizer_id', $organizer_id)->first();

        if ($attendee) {

            $order_exists = \App\Models\BillingOrder::where('attendee_id', $attendee['id'])
                ->where('event_id', $this->getOrderEventId())
                ->where('is_archive', '0')
                ->whereNotIn('status', ['cancelled', 'draft'])
                ->orderBy('id', 'DESC')
                ->currentOrder()
                ->first();

            // A valid order for this attendee already exists so do not proceed
            if ($order_exists) {
                return false;
            }
            
        }

        return true;
    }

    public function getTicketTypeAddonsIds()
    {
        // Function to get addons ids which are linked to ticket for all order attendees.
        // THIS SHOULD ONLY BE CALLED AFTER ORDER PLACEMENT.
        if (!$this->isOrderPlaced()) {return false;}
        $addon_ids = [];
        foreach ($this->getAllAttendees() as $attendee) {
            // EBOrderAttendee
            foreach ($attendee->getItems() as $item) {
                // EBOrderItem
                if ($item->getModel()->ticket_item_id > 0) {
                    $addon_ids[] = $item->getModel()->id;
                }
            }
        }

        return $addon_ids;
    }

    public function _generateOrderNumber()
    {
        $new_ord_number = \App\Models\EventsitePaymentSetting::where('event_id', '=', $this->getOrderEventId())->where('registration_form_id', 0)->value('eventsite_invoice_currentnumber');

        //When new event created, this returns empty
        if ($new_ord_number < 1) {
            $new_ord_number = 1;
        }
        
        if($this->_payment_setting) {
            $this->_payment_setting->eventsite_invoice_currentnumber = ($new_ord_number + 1);
            $this->_payment_setting->save();
        }

        return $new_ord_number;
    }

    public function updateModelAttribute($field, $value)
    {
        $this->_model->{$field} = $value;
    }
    
    private function _setupModelAttributes()
    {
        $currency = $this->getPaymentSettingAttribute('eventsite_currency');

        $fields = array(
            'attendee_types' => $this->_isEdit() ? $this->getModelAttribute('attendee_types') : $this->getUtility()->getData('attendee_types'),
            'is_new_flow' => request()->is_new_flow,
            'draft_at' => request()->utc_date_time,
            'event_id' => $this->getOrderEventId(),
            'language_id' => $this->getUtility()->getLangaugeId(),
            'session_id' => $this->getUtility()->getSessionID(),
            'security' => 'yes',
            'sale_agent_id' => $this->_isEdit() ? $this->getModelAttribute('sale_agent_id') : ($this->getUtility()->getSalesAgentID() ? $this->getUtility()->getSalesAgentID() : 0), // sale agent id
            'sale_type' => $this->getUtility()->isDraft() ? ($this->getModelAttribute('sale_type') ? $this->getModelAttribute('sale_type') : '') : (request()->sale_type ? request()->sale_type : ''),
            'vat' => ($this->getPaymentSettingAttribute('eventsite_apply_multi_vat') == 0) ? $this->getVatPercentage() : $this->getVATsDetail()->isVatApplied(), // Set 1/0 vat apply or not for new vat flow
            'vat_amount' => round($this->getVATGrandTotal(), 2), // Total order vat
            'grand_total' => round($this->getOrderGrandTotal(), 2),
            'corrected_total' => round($this->getOrderGrandTotal(true), 2),
            'reporting_panel_total' => round($this->getItemsTotal(), 2),
            'summary_sub_total' => round($this->getItemsTotal(), 2),
            'total_attendee' => count((array)$this->getAttendees()),
            'discount_type' => $this->getVoucherDiscountAmount() > 0 ? 'order' : '', //order voucher discount type
            'discount_amount' => round($this->getVoucherDiscountAmount(), 2), // Gained discount for applied order voucher
            'quantity_discount' => round($this->getQuantityDiscountAmount(), 2),
            'order_date' => $this->_isEdit() && !in_array($this->getUtility()->getPanel(), ['attendee', 'embed']) ? $this->getModelAttribute('order_date') : \Carbon\Carbon::now(), 
            'eventsite_currency' => $currency,
            'billing_quantity' => '',
            'order_type' => ($this->_isEdit() && !in_array($this->getUtility()->getPanel(), ['attendee', 'embed']) ? $this->getModelAttribute('order_type') : ($this->isFree() ? 'order' : 'invoice')),
            'is_free' => (int) $this->isFree(),
            'is_waitinglist' => (!$this->_isEdit() ? (int) $this->getUtility()->getData('is_waiting') : ($this->getModelAttribute('is_waitinglist') == 1 && $this->getModelAttribute('status') == 'completed' ? 0 : $this->getModelAttribute('is_waitinglist'))),
            'security_key' => (!$this->_isEdit() ? md5(Str::random(15)) : $this->getModelAttribute('security_key')),
            'is_tango' => 0,
            'e_invoice' => 0, // Automatically send ean after new order
            'user_agent' => serialize($_SERVER),
            'session_data' => serialize($this->getUtility()->getData()),
            'is_updated' => 1, // For insight events CSV exports, This is important.
            'order_number' => !$this->getUtility()->isDraft() ? $this->_generateOrderNumber() : ($this->getModelAttribute('order_number') ? $this->getModelAttribute('order_number') : ''),
            'item_level_vat' => ($this->getPaymentSettingAttribute('eventsite_apply_multi_vat') == 0) ? 0 : 1,
        );

        foreach ($fields as $field => $value) {
            $this->_model->{$field} = $value;
        }
    }

    private function _setNewImplementationFlag()
    {
        $this->_model->new_imp_flag = 1;
    }

    /**
     * @return [type]
     */
    public function _resetVats() {
        \App\Models\BillingOrderVAT::where('order_id', $this->_model->id)->delete();
    }

    private function updateWaitingListAttendeeStatus()
    {
        $attendee = \App\Models\WaitingListAttendee::where('event_id','=',$this->getOrderEventId())->where('attendee_id','=',$this->getMainAttendee()->getModel()->id)->first();
        
        if($attendee) {
            $attendee->status = '2';
            $attendee->save();
        }
    }

    public function save()
    {
        if ($this->isOrderPlaced()) {
            throw new \Exception('Order is already placed.');
        }

        if ($this->_isEdit()) { 

            if($this->getUtility()->isDraft()) {

                $this->_setupModelAttributes();

                $this->_setNewImplementationFlag();

                $this->_model->save();

                $this->_saveAttendees();

                $this->_saveHotel();

                $this->_saveVAT();

                $this->_updateDraftOrder();

                $this->_saveQtyDiscountRuleLog();

            } else {
                
                if(in_array($this->getUtility()->getPanel(), ['attendee', 'embed'])) {
                    //Registration site

                    if($this->getModelAttribute('is_waitinglist') == 1 && $this->getModelAttribute('status') == 'completed') {
                        $this->updateWaitingListAttendeeStatus();
                    }

                    $this->_setupModelAttributes();
                    
                    $this->_setNewImplementationFlag();

                    $this->_model->save();

                    event(Event::OrderAttendeeBeforeSaveInstaller, $this);

                    $this->_saveAttendees();

                    event(Event::OrderAttendeeAfterSaveInstaller, $this);

                    event(Event::OrderAttendeeLogAfterSaveInstaller, [$this, 'add']);

                    $this->_saveHotel();

                    $this->_saveVAT();

                    $this->_updateAfterSave();
                    
                    $this->_setStatePlaced();

                    event(Event::OrderGeneralActionInstaller, $this);

                    event(Event::addReportingRevenueInstaller, $this);
                    
                    event(Event::OrderKeywordsSaveInstaller, $this);

                    $this->_saveQtyDiscountRuleLog();
                    
                } else {
                    //Edit order

                    $this->_setupModelAttributes();

                    $this->_setNewImplementationFlag();
        
                    $this->_model->save();
        
                    event(Event::OrderAttendeeBeforeSaveInstaller, $this);
                    
                    $this->_saveAttendees();
        
                    $this->_assignEventAttendees();
                    
                    event(Event::OrderAttendeeAfterSaveInstaller, $this);

                    event(Event::OrderAttendeeLogAfterSaveInstaller, [$this, 'update']);

                    $this->_saveHotel();
        
                    $this->_saveVAT();
        
                    event(Event::OrderUpdateAfterInstaller, $this);
        
                    $this->_updateAfterSave();

                    $this->_setStatePlaced();

                    if($this->getModelAttribute('parent_id') == 0) {
                        event(Event::addReportingRevenueInstaller, $this); // First time order created
                    } 
                    
                    if(in_array($this->getUtility()->getPanel(), ['sale'])) {
                        event(Event::OrderGeneralActionInstaller, $this);
                    }

                    event(Event::OrderKeywordsSaveInstaller, $this);
                }
            }
            
        } else {
            //Creating new order first time
            if (!$this->_isEmailValidForSystem()) {
                throw new \Exception('Order with this email already exists.');
            } else {
                if($this->getUtility()->isDraft()) {

                    $this->_setupModelAttributes();

                    $this->_setNewImplementationFlag();

                    $this->_model->save();

                    $this->_saveAttendees();

                    $this->_saveHotel();

                    $this->_saveVAT();

                    $this->_updateDraftOrder();
                    
                    $this->_saveQtyDiscountRuleLog();

                    //Save request logs
                    //$this->saveOrderLogs($this->getUtility()->getAllData());
                } else {

                    //Create order api
                    if($this->getModelAttribute('is_waitinglist') == 1 && $this->getModelAttribute('status') == 'completed') {
                        $this->updateWaitingListAttendeeStatus();
                    }

                    $this->_setupModelAttributes();
                    
                    $this->_setNewImplementationFlag();

                    $this->_model->save();

                    event(Event::OrderAttendeeBeforeSaveInstaller, $this);

                    $this->_saveAttendees();

                    event(Event::OrderAttendeeAfterSaveInstaller, $this);

                    event(Event::OrderAttendeeLogAfterSaveInstaller, [$this, 'add']);

                    $this->_saveHotel();

                    $this->_saveVAT();

                    $this->_updateAfterSave();
                    
                    $this->_setStatePlaced();

                    event(Event::OrderGeneralActionInstaller, $this);

                    event(Event::addReportingRevenueInstaller, $this);
                    
                    event(Event::OrderKeywordsSaveInstaller, $this);

                    $this->_saveQtyDiscountRuleLog();
                    
                }
            }
        }
    }

    public function getAttendees()
    {
        return $this->_attendees;
    }

    public function getOrderGrandTotal($without_hotel = false)
    {
        $total = $this->getItemsTotal() + $this->getHotelSubTotal() + $this->getVATGrandTotal();

        if ($without_hotel) {
            $total = $this->_getGrandTotalWithoutHotel();
        }

        return round($total, 2);
    }

    private function _getGrandTotalWithoutHotel()
    {
        $items_total = $this->getItemsTotal() + $this->getItemsVATAmount();
        return round($items_total, 2);
    }

    public function getGrandTotalWithoutVAT()
    {
        return round($this->getItemsTotal() + $this->getHotelSubTotal(), 2);
    }

    public function getVoucherDiscountAmount()
    {
        foreach ($this->getDiscounts() as $discount) {
            // EBOrderDiscount
            if ($discount->getDiscountType() == 'voucher') {
                return round($discount->getDiscountAmount(), 2);
            }
        }

        return 0;
    }

    public function getVoucherDiscountType()
    {
        foreach ($this->getDiscounts() as $discount) {
            // EBOrderDiscount
            if ($discount->getDiscountType() == 'voucher') {
                return $discount->getDiscountObject()->getType();
            }
        }

        return 0;
    }

    public function getVoucherDiscountPrice()
    {
        foreach ($this->getDiscounts() as $discount) {
            // EBOrderDiscount
            if ($discount->getDiscountType() == 'voucher') {
                return round($discount->getDiscountObject()->getPrice(), 2);
            }
        }

        return 0;
    }

    public function getQuantityDiscountAmount()
    {
        $discount_amount = 0;

        foreach ($this->getDiscounts() as $discount) {
            // EBOrderDiscount
            if ($discount->getDiscountType() == 'quantity') {
                $discount_amount = $discount_amount + round($discount->getDiscountAmount(), 2);
            }
        }

        return $discount_amount;
    }

    public function getItemQuantityDiscountAmount(EBOrderItem $item)
    {
        foreach ($this->getDiscounts() as $discount) {
            // EBOrderDiscount
            if ($discount->getDiscountType() == 'quantity' && $discount->getDiscountObject()->getItem()->getBillingItemId() == $item->getBillingItemId()) {
                return round($discount->getDiscountAmount(), 2);
            }
        }
        return 0;
    }

    public function getHotelVAT()
    {
        if (count($this->getHotel()) > 0) {
            foreach ($this->getVATs() as $VAT) {
                // EBOrderVAT
                if ($VAT->getVatType() == 'hotel') {
                    return $VAT;
                }
            }
        }

        return null;
    }

    public function getHotelVATAmount()
    {
        return ($this->getHotelVAT() instanceof EBOrderVAT) ? round($this->getHotelVAT()->getVatAmount(), 2) : 0;
    }

    public function getItemsVAT()
    {
        foreach ($this->getVATs() as $VAT) {
            // \App\Eventbuizz\EBObjects\EBOrderVAT
            if ($VAT->getVatType() == 'items') {
                return $VAT;
            }
        }

        return null;
    }

    public function getItemsVATAmount()
    {
        return ($this->getItemsVAT() instanceof EBOrderVAT) ? round($this->getItemsVAT()->getVatAmount(), 2) : 0;
    }

    public function getVATGrandTotal()
    {
        $total = 0;

        foreach ($this->getVATs() as $VAT) {
            // \App\Eventbuizz\EBObjects\EBOrderVAT
            $total += $VAT->getVatAmount();
        }
        
        return round($total, 2);
    }

    public function toArray()
    {
        $arr = [];

        foreach (get_object_vars($this) as $key => $var) {
            if (is_object($var)) {
                foreach (get_object_vars($var) as $k => $v) {
                    $arr[$key][$k] = $v;
                }
            } else {
                $arr[$key] = $var;
            }
        }
    }

    public function getAllAttendees()
    {
        return $this->_attendees;
    }

    public function getModel()
    {
        return $this->_model;
    }

    private function _doesAttendeeExist($id)
    {
        foreach ($this->getAllAttendees() as $attendee) {
            // EBOrderAttendee
            if ($attendee->getModel()->id == $id) {
                return true;
            }
        }

        return false;
    }

    //Returns Array of items that needs to be added to that attendee
    private function _getNewItemsAddedForAttendee(EBOrderAttendee $attendee)
    {
        if ($this->_isEdit() == false) {
            return [];
        }

        $attendee_id = $attendee->getModel()->id;
        
        $input = request()->attendee;

        $attendee_items = $input[$attendee_id];

        $items_array = $this->_getAttendeeItemIds($attendee_items);

        if (count($items_array) < 1) {
            return [];
        }
        
        $attendee_existing_items = [];

        foreach ($attendee->getItems() as $item) {
            $attendee_existing_items[] = $item->getBillingItemId();
        }

        return array_diff($items_array, $attendee_existing_items);
    }

    private function _isItemRemovedByUser($attendee_id, $item_id)
    {
        $input = request()->attendee;

        $attendee_items = $input[$attendee_id];

        if (count($attendee_items) < 1) {
            return true;
        }

        $items_array = $this->_getAttendeeItemIds($attendee_items);
        
        return (array_search($item_id, $items_array) === false);
    }

    //This function convert multi dimensional array into single array of item ids
    private function _getAttendeeItemIds($attendee_items)
    {
        if (!is_array($attendee_items)) {
            return false;
        }

        $result = array();

        foreach ($attendee_items as $item) {
            $result[] = $item['id'];
        }

        return $result;
    }

    //This function convert multi dimensional array into single array of items
    private function _getAttendeeItems($attendee_id, $new_items)
    {
        $input = request()->attendee;

        $attendee_items = $input[$attendee_id];

        if (count($attendee_items) < 1) {
            return [];
        }

        $result = array();

        foreach ($attendee_items as $item) {
            if(in_array($item['id'], $new_items)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    private function _getInputForItem(EBOrderItem $item, $attendee_id)
    {
        $input = request()->attendee;
        $attendee_items = $input[$attendee_id];
        $find_item = (array) array_values(Arr::where($attendee_items, function ($row, $key) use($item) {
            return $row['id'] == $item->getBillingItemId();
        }));
        if(count($find_item) > 0) {
            return $find_item[0];
        } else {
            return null;
        }
    }

    private function removeOrderItemRuleLogs()
    {
        \App\Models\BillingOrderRuleLog::where('order_id', $this->getModelAttribute('id'))->delete();
    }

    public function deleteAttendee($attendee_id)
    {
        foreach ($this->getAllAttendees() as $key => $attendee) {
            if($attendee_id && $attendee_id == $attendee->getModel()->id) {
                $this->_removeAttendeeByID($attendee->getModel()->id);
                $this->removeAllAttendeeOrderData($this->_model->id, $attendee_id);
            }
        }
        
        $this->removeHotelsNotAttachedWithAttendees();
    }

    /**
     * @return [type]
     */
    public function updateOrder()
    {
        //Save request logs
        //$this->saveOrderLogs(request()->only(['action', 'attendee', 'draft', 'attendee_id', 'attendee_qty', 'panel', 'hotel_data', 'update_hotel_state', 'order_hotel_id', 'voucher_code']));

        $this->_setStateInProgress();

        $this->setIsEdit();

        //Voucher apply
        if(request()->voucher_code) {
            $this->applyVoucher();
        }
        
        //Remove voucher
        if(request()->action == "remove-voucher") {
            $this->removeVoucher();
        }

        $this->_setupItemVoucherLImits();
        
        //Actions

        //Update attendee iteration completed
        if(request()->attendee_id && request()->action == "attendee-iteration-completed") {
            $this->_getAttendeeByID(request()->attendee_id)->completeAttendeeIteration();
            return;
        }

        //Add new attendee
        if(request()->attendee && request()->action == "add-attendee") {

            //order
            $attendee = request()->attendee;

            $this->_attendees[] = new EBOrderAttendee($this, $attendee, []);
        }

        //Delete attendee
        if(request()->action == "delete-attendee") {
            $this->deleteAttendee(request()->attendee_id);
        }

        //Update attendee
        if(request()->attendee && request()->attendee_id && request()->action == "update-attendee") {

            $input = request()->attendee;
            
            $attendee = $this->_getAttendeeByID(request()->attendee_id);

            //If attendee email updated 
            if(trim(Str::lower($input['email'])) != trim(Str::lower($attendee->getModel()->email))) {
                
                // New attendee created
                $new_attendee = new EBOrderAttendee($this, $input, []);
                $new_attendee->save();

                $this->_attendees[] = $new_attendee;
                
                //if attendee type change during update [Remove all order data for attendee]
                $order_attendee = $attendee->getOrderAttendee();

                if($order_attendee->attendee_type != request()->attendee_type) {
                    $this->removeAllAttendeeOrderData($this->_model->id, request()->attendee_id);
                } else {
                    // Update all attendee data while updating attendee
                    $this->updateAllAttendeeOrderData($this->_model->id, request()->attendee_id, $new_attendee->getModel()->id);
                } 

                //Remove attendee from attendees list otherwise we will get error during order saving for this old attendee [ex: unset($this->_attendees[$key]) ]
                $this->_removeAttendeeByID($attendee->getModel()->id);
                
                $this->removeHotelsNotAttachedWithAttendees();

            } else {
                //if attendee type change during update [Remove all order data for attendee]
                $order_attendee = $attendee->getOrderAttendee();

                if($order_attendee->attendee_type != request()->attendee_type) {
                    $this->removeAllAttendeeOrderData($this->_model->id, request()->attendee_id);
                    $this->_removed_attendees[] = request()->attendee_id;
                    $this->removeHotelsNotAttachedWithAttendees();
                }
                
                $this->_getAttendeeByID(request()->attendee_id)->updateInfo(request()->attendee, 'basic');
                $this->_getAttendeeByID(request()->attendee_id)->updateInfo(request()->attendee, 'info');
                $this->_getAttendeeByID(request()->attendee_id)->updateInfo(request()->attendee, 'attendee_type');
                $this->_getAttendeeByID(request()->attendee_id)->updateInfo(request()->attendee, 'order_attendee_fields');

                return; 
            }

        }

        //Update attendee billing
        if(request()->attendee && request()->attendee_id && request()->action == "update-attendee-billing") {
            $this->_getAttendeeByID(request()->attendee_id)->updateInfo(request()->attendee, 'billing');
        }

        //Delete attendee item
        if(request()->attendee && request()->action == "delete-attendee-item") {
            foreach ($this->getAllAttendees() as $key => $attendee) {
                if(request()->attendee_id && request()->attendee_id == $attendee->getModel()->id) {
                    foreach ($attendee->getItems() as $item) {
                        if (request()->item_id && request()->item_id == $item->getBillingItemId()) {
                            // Remove item
                            $attendee->removeItem($item->getBillingItemId());
          
                            if(isset($this->_removed_attendee_items[$attendee->getModel()->id])) {
                                array_push($this->_removed_attendee_items[$attendee->getModel()->id], $item->getBillingItemId());
                            } else {
                                $this->_removed_attendee_items[$attendee->getModel()->id] = [$item->getBillingItemId()];
                            }
        
                            continue;
                        }
                    }
                }
            }
        }
        
        //Update attendee items
        if(request()->attendee && request()->action == "update-attendee-items") {

            //Remove order item rule logs
            $this->removeOrderItemRuleLogs();
            
            foreach ($this->getAllAttendees() as $key => $attendee) {
                if(request()->attendee_id && request()->attendee_id == $attendee->getModel()->id) {
                    foreach ($attendee->getItems() as $item) {
                        if ($this->_isItemRemovedByUser($attendee->getModel()->id, $item->getBillingItemId())) {
                            
                            // Remove item
                            $attendee->removeItem($item->getBillingItemId());  

                            if(isset($this->_removed_attendee_items[$attendee->getModel()->id])) {
                                array_push($this->_removed_attendee_items[$attendee->getModel()->id], $item->getBillingItemId());
                            } else {
                                $this->_removed_attendee_items[$attendee->getModel()->id] = [$item->getBillingItemId()];
                            }

                            continue;
                        }

                        $inputItem = $this->_getInputForItem($item, $attendee->getModel()->id);

                        if($inputItem) {
                            $item->updateQtyFromInput((int)$inputItem['quantity']);
                            $item->updatePriceFromInput((float)$inputItem['price']);

                            //Discount 
                            if((float)$inputItem['discount'] > 0 && $inputItem['discount_type'] == 3) {
                                $item->updateModelAttribute('discount_type', $inputItem['discount_type']);
                                $item->updateModelAttribute('discount_qty', $inputItem['quantity']);
                                $item->updateModelAttribute('discount', $inputItem['discount']);
                            } else {
                                $item->updateModelAttribute('discount_type', 0);
                                $item->updateModelAttribute('discount_qty', 0);
                                $item->updateModelAttribute('discount', 0);
                            }
                        }
                        
                        $item->updateDiscounts(); 
                    }
                    if (count($this->_getNewItemsAddedForAttendee($attendee)) > 0) {
                        $new_items = $this->_getAttendeeItems($attendee->getModel()->id, $this->_getNewItemsAddedForAttendee($attendee));
                        if(count($new_items)) {
                            foreach ($new_items as $item) {
                                $item['qty'] = $item['quantity'];
                                $attendee->addItem($item);
                            }
                        }
                    }
                }
            }
        }

        $this->setNewMainAttendeeIfRemoved();
        
        //Update attendee item discount
        foreach ($this->getAllAttendees() as $key => $attendee) {
            foreach ($attendee->getItems() as $item) {
                $item->updateDiscounts();
            }
        }
        
        //unset item
        unset($this->_items);
    
        //set items
        $this->_setItems();

        //Add hotel
        if(request()->hotel_data && request()->action == "add-hotel") {
            // Hotel added during edit order
            if ($this->_isEdit() && $this->_isHotelAddedEditOrder()) {
                //Setup new hotel
                $this->_setHotel();
            } 
        }

        //Remove hotel
        if(request()->action == "remove-hotel" && $this->_isEdit() && request()->order_hotel_id) {
            foreach ($this->getHotel() as $key => $hotel) {
                if(request()->order_hotel_id && request()->order_hotel_id == $hotel->getModel()->id) {
                    $this->_removed_hotels[] = $hotel->getModel()->id;
                    // Remove this hotel
                    unset($this->_hotel[$key]);
                }
            }
        }
        //End

        //Reset vats
        $this->_resetVats();

        $this->_setStateInProgress();
        $this->_discounts = []; //reset any existing order discounts
        $this->_setupOrderDiscounts(); //setup order discounts again
        $this->_VATs = []; //reset any exisiting VATs
        $this->_setVATs();

        return $this;
    }
    
    /**
     * @return [type]
     */
    public function getOrderInvoicePDF()
    {
        // Creates and returns pdf file to order invoice. This will return false if is_waitinglist or is_tango flag is set to 1.

        if ($this->getModelAttribute('is_waitinglist') == '1' || $this->getModelAttribute('is_tango') == '1') {
            return false;
        }

        $event_id = $this->getOrderEventId();

        $language_id = $this->getUtility()->getLangaugeId();

        $eventSetting = $this->_getEventSetting();

        $payment_setting = $this->_getPaymentSetting();

        $eventsite_setting = $this->_getEventSiteSetting();

        //labels
        $labels = eventsite_labels(['eventsite', 'exportlabels'], ["event_id" => $this->getOrderEventId(), "language_id" => $this->getUtility()->getLangaugeId()]);

        global $order_detail;

        $invoiceHtml = \App\Eventbuizz\Repositories\EventsiteBillingOrderRepository::getOrderDetailInvoice("html", $this, $labels, $this->getUtility()->getLangaugeId(), $this->getOrderEventId(), $payment_setting['eventsite_currency'], $this->getModelAttribute('id'), 1, 1, true, false, 0, $this->getModelAttribute('is_archive'));

        $sections = \App\Eventbuizz\Repositories\EventSiteSettingRepository::getAllSectionFields(["event_id" => $event_id, "language_id" => $this->getUtility()->getLangaugeId()]);

        foreach ($sections as $section) {
            foreach ($section as $key => $field) {
                $billing_fields[$key] = $field['name'];
            }
        }

        //PDF
        $pdf = 1;

        $filename = sanitizeLabel($labels['EXPORT_ORDER_INVOICE']) . '_' . $order_detail['order']['order_number'] . '_' . time() . '.pdf';

        $file_to_save = config('cdn.cdn_upload_path') . 'assets' . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $filename;

        $snappy = \PDF::loadHTML($invoiceHtml)->setPaper('a4');
        
        $snappy->setOption('header-html',\View::make('admin.order.order_history.invoice.header', compact('eventSetting', 'pdf', 'billing_fields', 'order_detail'))->render());

        if(strlen(trim(stripslashes($payment_setting['footer_text']))) > 0)
        {
            $snappy->setOption('footer-html',\View::make('admin.order.order_history.invoice.footer', compact('eventSetting', 'pdf', 'payment_settings', 'order_detail'))->render());
        }

        $snappy->setOption('print-media-type', true);

        $snappy->setOption('margin-right',0);

        $snappy->setOption('margin-left',0);

        $snappy->save($file_to_save);

        return file_exists($file_to_save) == true ? $file_to_save : false;
    }

    public function _getAttendeeByID($att_id)
    {
        foreach ($this->getAllAttendees() as $att) {
            /* @var $att EBOrderAttendee */
            if ($att->getModel()->id == $att_id) {
                return $att;
            }
        }
        return false;
    }

    public function _removeAttendeeByID($att_id)
    {
        foreach ($this->getAllAttendees() as $key => $att) {
            /* @var $att EBOrderAttendee */
            if ($att->getModel()->id == $att_id) {
                unset($this->_attendees[$key]); 
                //Remove all attendee old data while saving attendee 
                $this->_removed_attendees[] = $att_id;
            }
        }
    }

    public function _getAttendeeByEmail($email)
    {
        foreach ($this->getAllAttendees() as $att) {
            /* @var $att EBOrderAttendee */
            if ($att->getModel()->email == $email) {
                return $att;
            }
        }
        return false;
    }

    public function _countCompletedOrderAttendees()
    {
        $count = 0;
        foreach ($this->getAllAttendees() as $att) {
            if ($att->getOrderAttendee()->status == "complete") {
                $count = $count + 1;
            }
        }
        return $count;
    }

    public function setTransactionID($tid)
    {
        if (is_null($tid)) {
            $tid = ''; //to avoid error upon saving with null value
        }

        $this->_model->transaction_id = $tid;
    }

    public function setPaymentResponse($response)
    {
        $this->_model->payment_response = $response;
    }

    public function setOrderStatus($status)
    {
        $validStatuses = ['completed', 'cancelled', 'pending', 'accepted', 'rejected', 'draft'];
        if (in_array($status, $validStatuses) !== false) {
            $this->_model->status = $status;
        }
    }

    public function setOrderPaymentReceived($status)
    {
        $status = (int) ((bool) $status);
        $this->_model->is_payment_received = $status;
        if ($status == 1) {
            $this->_model->payment_received_date = \Carbon\Carbon::now();
        }
    }

    /**
     * @return EBOrder|null
     */
    public function getPreviousVersion()
    {
        return ($this->_previous_version instanceof EBOrder) ? $this->_previous_version : null;
    }

    public function setPreviousVersion(EBOrder $order)
    {
        $this->_previous_version = ($order instanceof EBOrder) ? $order : null;
    }

    /**
     * @param $file
     * @return bool
     */
    public function setTicketsPDFFile($file)
    {
        if (!is_file($file) || !file_exists($file)) { return false; }
        $this->_tickets_pdf_file = $file;
        return true;
    }

    /**
     * only available after order placement only. Will not return
     * @return string file path
     */
    public function getTicketsPDFFile()
    {
        return file_exists($this->_tickets_pdf_file) ? $this->_tickets_pdf_file : false;
    }

    /**
     * sets the generated ticket ids into the order object so that pdf can be generated.
     * @param $arr
     * @return bool
     */
    public function setGeneratedTicketsIds($arr)
    {
        if (!is_array($arr) || count($arr) < 1) {
            return false;
        }

        foreach ($arr as $tid) {
            $this->_generated_tickets_ids[] = $tid;
        }
        return true;
    }

    public function getGeneratedTicketsIds()
    {
        return $this->_generated_tickets_ids;
    }

    /**
     * @param bool $sale_agent
     * 
     * @return [type]
     */
    public function getInvoiceSummary($sale_agent = false)
    {
        $currencies = getCurrencyArray();

        $currency = $this->getPaymentSettingAttribute('eventsite_currency');

        $this->_invoice_summary['order'] = $this->getModel();

        if ($this->_invoice_summary['order']['discount_amount'] != 0 && $this->_invoice_summary['order']['is_voucher']) {
            $this->_invoice_summary['order']['is_voucher'] = 0; //This is only used for when items based voucher applied
        }

        $this->_invoice_summary['order_event_detail'] = $this->getEvent();

        $this->_invoice_summary['vat_amount'] = $this->getVATGrandTotal();

        $this->_invoice_summary['order_main_attendee'] = $this->getModel()->order_attendee()->with(['info' => function ($query) {
            return $query->where('languages_id', '=', $this->getUtility()->getLangaugeId());
        }])->first()->toArray();

        $temp = array();

        foreach ($this->_invoice_summary['order_main_attendee']['info'] as $row) {
            $temp[$row['name']] = $row['value'];
        }

        $this->_invoice_summary['order_main_attendee']['info'] = $temp;

        $this->_invoice_summary['order_summary_detail'] = $this->_getOrderSummaryDetail();
        
        $this->_invoice_summary['show_display_discount_qty_col'] = $this->_invoice_summary['order_summary_detail']['show_display_discount_qty_col'];

        $this->_invoice_summary['attendee_summary_detail'] = $this->_getAttendeeSummaryDetail();

        $this->_invoice_summary['order_billing_detail'] = $this->getMainAttendee()->getBillingModel();

        $this->_invoice_summary['order_billing_detail']['billing_company_country_name'] = getCountryName($this->_invoice_summary['order_billing_detail']['billing_company_country']);

        $hotels = [];

        if (!is_null($this->getHotel())) {

            foreach ($this->getHotel() as $hotel) {

                if ($hotel instanceof EBOrderHotel) {

                    $h['id'] = $hotel->getModel()->id;

                    $h['name'] = $hotel->getModel()->name;

                    $event_hotel = $hotel->getHotelObject();

                    $event_hotel_info = $hotel->getHotelObject()->info()->where('name', 'description')->first()->toArray();

                    $h['description'] = isset($event_hotel_info['name']) ? $event_hotel_info['value'] : '';

                    $h['check_in'] = $hotel->getCheckin();

                    $h['check_out'] = $hotel->getCheckout();

                    $h['nights'] = days($hotel->getCheckin(), \Carbon\Carbon::parse($hotel->getCheckout()));

                    $h['date_range_display'] = \Carbon\Carbon::parse($hotel->getCheckin())->format('F j').' - '.\Carbon\Carbon::parse($hotel->getCheckout())->format('F j, Y');
                    
                    $h['rooms'] = $hotel->getRooms();

                    $h['price'] = round($hotel->getModel()->price, 2);

                    $h['price_type'] = $hotel->getModel()->price_type;

                    $h['sub_total'] = round($hotel->getHotelSubtotal(), 2);

                    $h['vat_rate'] = $hotel->getApplicableVATRate();

                    $h['vat_amount'] = round($hotel->getHotelVATAmount(), 2);

                    $h['grand_total'] = round($hotel->getHotelGrandTotal(), 2);

                    $h['persons'] = $hotel->getModel()->hotel_persons()->get();

                    $h['sub_total_display'] = getCurrency(round($hotel->getHotelSubtotal(), 2), $currencies[$currency]). ' ' . $currencies[$currency];
                    
                    $h['registration_form_id'] = $hotel->getModel()->registration_form_id;
                    
                    $h['payment_form_setting'] = $this->_getPaymentFormSetting($hotel->getModel()->registration_form_id, true);

                    $hotels[] = $h;
                }

            }

        }

        $this->_invoice_summary['is_hotel_attached'] = is_null($this->getHotel()) ? 0 : 1;

        $this->_invoice_summary['hotel'] = $hotels;

        $this->_invoice_summary['hotel_sub_total'] = $this->getHotelSubTotal();

        $this->_invoice_summary['is_vat_applied'] = empty($this->getVATGrandTotal()) ? 0 : 1;

        $vat_detail = $this->getVATsDetail();

        if ($vat_detail instanceof EBOrderVATDetail && $this->_invoice_summary['is_vat_applied']) {
            $this->_invoice_summary['vat_detail'] = $vat_detail->getVAT();
            $this->_invoice_summary['display_vat_detail'] = $this->displayVATsDetails();
        }

        $this->_invoice_summary['total_vat_amount'] = $this->getVATGrandTotal();

        $this->_invoice_summary['total_vat_amount_display'] = getCurrency($this->_invoice_summary['total_vat_amount'], $currencies[$currency]). ' ' . $currencies[$currency];

        //Sub total with discount
        $sub_total = $this->_invoice_summary['order']['grand_total'] - $this->_invoice_summary['total_vat_amount'];

        $this->_invoice_summary['sub_total'] = $sub_total;

        $this->_invoice_summary['sub_total_display'] = getCurrency($sub_total, $currencies[$currency]). ' ' . $currencies[$currency];
        //End sub total with discount

        //Sub total without order base discount
        $sub_total_without_discount = ($this->_invoice_summary['order']['grand_total'] + $this->_invoice_summary['order']['discount_amount']) - $this->_invoice_summary['total_vat_amount'];

        $this->_invoice_summary['sub_total_without_discount'] = $sub_total_without_discount;

        $this->_invoice_summary['sub_total_without_discount_display'] = getCurrency($sub_total_without_discount, $currencies[$currency]). ' ' . $currencies[$currency];
        //End sub total without order base discount

        $this->_invoice_summary['discount_amount'] = getCurrency($this->_invoice_summary['order']['discount_amount'], $currencies[$currency]). ' ' . $currencies[$currency];
        
        $this->_invoice_summary['grand_total_display'] = getCurrency($this->getModel()->grand_total, $currencies[$currency]). ' ' . $currencies[$currency];

        return $this->_invoice_summary;
    }

    /**
     * @return [type]
     */
    private function _getOrderSummaryDetail()
    {
        $group_ids = [];
        $group_addon_ids = [];
        $group_addons = [];
        $single_addon_ids = [];
        $single_addons = [];
        $show_display_discount_qty_col = $show_display_discount_col = 0;
        $currencies = getCurrencyArray();
        $currency = $this->getPaymentSettingAttribute('eventsite_currency');

        foreach ($this->getAllItems() as $key => $item) {
            if ($item->getModel()->group_id) {

                if (!in_array($item->getModel()->group_id, $group_ids)) {
                    $group_ids[] = $item->getModel()->group_id;
                    $group_index = array_search($item->getModel()->group_id, $group_ids);
                    $addon_group_detail = $item->getModel()->addon_group_detail()->with(['info' => function ($query) {
                        return $query->where('languages_id', '=', $this->getUtility()->getLangaugeId());
                    }])->first()->toArray();
                    $group_addons[$group_index]['group_id'] = $item->getModel()->group_id;
                    $group_addons[$group_index]['group_name'] = isset($addon_group_detail['info'][0]['value']) ? $addon_group_detail['info'][0]['value'] : '';
                    $group_addons[$group_index]['addons'] = [];
                }
                
                if (!in_array($item->getModel()->addon_id, $group_addon_ids)) {
                    $group_addon_ids[] = $item->getModel()->addon_id;
                    $group_index = array_search($item->getModel()->group_id, $group_ids);
                    $addon_index = array_search($item->getModel()->addon_id, $group_addon_ids);
                    $group_addons[$group_index]['addons'][$addon_index]['discount'] = 0;
                    $group_addons[$group_index]['addons'][$addon_index]['qty'] = 0;
                    $group_addons[$group_index]['addons'][$addon_index]['subtotal'] = 0;
                    $group_addons[$group_index]['addons'][$addon_index]['subtotal_with_discount'] = 0;
                }

                $item_log = \App\Models\BillingOrderRuleLog::where('order_id', '=', $this->getModelAttribute('id'))->where('item_id', '=', $item->getModel()->addon_id)->first();
                
                if ($item_log) $show_display_discount_qty_col = 1;

                $quantity_discount = $item_log->item_discount ? $item_log->item_discount : 0;

                $group_index = array_search($item->getModel()->group_id, $group_ids);
                $addon_index = array_search($item->getModel()->addon_id, $group_addon_ids);
                $group_addons[$group_index]['addons'][$addon_index]['addon_id'] = $item->getModel()->addon_id;
                $group_addons[$group_index]['addons'][$addon_index]['name'] = $item->getItemName();
                $group_addons[$group_index]['addons'][$addon_index]['description'] = $item->getBillingItem()->info()->where('name', '=', 'description')->value('value');
                $group_addons[$group_index]['addons'][$addon_index]['price'] = round($item->getPriceUnit(), 2);
                $group_addons[$group_index]['addons'][$addon_index]['price_display'] = getCurrency(round($item->getPriceUnit(), 2), $currencies[$currency]). ' ' . $currencies[$currency];
                $group_addons[$group_index]['addons'][$addon_index]['discount'] += round($item->getDiscountAmount(), 2);
                $group_addons[$group_index]['addons'][$addon_index]['discount_display'] = getCurrency($group_addons[$group_index]['addons'][$addon_index]['discount'], $currencies[$currency]). ' ' . $currencies[$currency];
                $group_addons[$group_index]['addons'][$addon_index]['qty'] += $item->getQuantity();
                $group_addons[$group_index]['addons'][$addon_index]['subtotal'] += round($item->getPriceSubtotal(), 2);
                $group_addons[$group_index]['addons'][$addon_index]['subtotal_display'] = getCurrency($group_addons[$group_index]['addons'][$addon_index]['subtotal'], $currencies[$currency]). ' ' . $currencies[$currency];
                $group_addons[$group_index]['addons'][$addon_index]['subtotal_with_discount'] += round($item->getPriceSubtotalWithDiscount(), 2);
                $group_addons[$group_index]['addons'][$addon_index]['subtotal_with_discount_display'] = getCurrency($group_addons[$group_index]['addons'][$addon_index]['subtotal_with_discount'], $currencies[$currency]). ' ' . $currencies[$currency];
                $group_addons[$group_index]['addons'][$addon_index]['quantity_discount'] = round($quantity_discount, 2);
                $group_addons[$group_index]['addons'][$addon_index]['quantity_discount_display'] = getCurrency($quantity_discount, $currencies[$currency]). ' ' . $currencies[$currency];
                $group_addons[$group_index]['addons'][$addon_index]['grand_total'] = round(($group_addons[$group_index]['addons'][$addon_index]['subtotal_with_discount'] - $group_addons[$group_index]['addons'][$addon_index]['quantity_discount']), 2);
                $group_addons[$group_index]['addons'][$addon_index]['grand_total_display'] = getCurrency(round(($group_addons[$group_index]['addons'][$addon_index]['subtotal_with_discount'] - $group_addons[$group_index]['addons'][$addon_index]['quantity_discount']), 2), $currencies[$currency]). ' ' . $currencies[$currency];
                $group_addons[$group_index]['addons'][$addon_index]['vat'] = $item->getModel()->vat;
                $group_addons[$group_index]['addons'][$addon_index]['item_number'] = $item->getBillingItem()->item_number;

                $group_addons[$group_index]['addons'][$addon_index]['link_data'] = EventsiteBillingItemRepository::itemLinkToWithDetail(['event_id' => $this->getOrderEventId(), 'language_id' => $this->getUtility()->getLangaugeId()], $item->getModel());

                if($item->getDiscountAmount() > 0) $show_display_discount_col = 1;

            } else {

                if (!in_array($item->getModel()->addon_id, $single_addon_ids)) {
                    $single_addon_ids[] = $item->getModel()->addon_id;
                    $addon_index = array_search($item->getModel()->addon_id, $single_addon_ids);
                    $single_addons[$addon_index]['discount'] = 0;
                    $single_addons[$addon_index]['qty'] = 0;
                    $single_addons[$addon_index]['subtotal'] = 0;
                    $single_addons[$addon_index]['subtotal_with_discount'] = 0;
                }

                $item_log = \App\Models\BillingOrderRuleLog::where('order_id', '=', $this->getModelAttribute('id'))->where('item_id', '=', $item->getModel()->addon_id)->first();
                
                if ($item_log) $show_display_discount_qty_col = 1;

                $quantity_discount = $item_log->item_discount ? $item_log->item_discount : 0;

                $addon_index = array_search($item->getModel()->addon_id, $single_addon_ids);
                $single_addons[$addon_index]['addon_id'] = $item->getModel()->addon_id;
                $single_addons[$addon_index]['name'] = $item->getItemName();
                $single_addons[$addon_index]['description'] = $item->getBillingItem()->info()->where('name', '=', 'description')->value('value');
                $single_addons[$addon_index]['price'] = round($item->getPriceUnit(), 2);
                $single_addons[$addon_index]['price_display'] = getCurrency(round($item->getPriceUnit(), 2), $currencies[$currency]). ' ' . $currencies[$currency];
                $single_addons[$addon_index]['discount'] += round($item->getDiscountAmount(), 2);
                $single_addons[$addon_index]['discount_display'] = getCurrency($single_addons[$addon_index]['discount'], $currencies[$currency]). ' ' . $currencies[$currency];
                $single_addons[$addon_index]['qty'] += $item->getQuantity();
                $single_addons[$addon_index]['subtotal'] += round($item->getPriceSubtotal(), 2);
                $single_addons[$addon_index]['subtotal_display'] = getCurrency($single_addons[$addon_index]['subtotal'], $currencies[$currency]). ' ' . $currencies[$currency];
                $single_addons[$addon_index]['subtotal_with_discount'] += round($item->getPriceSubtotalWithDiscount(), 2);
                $single_addons[$addon_index]['subtotal_with_discount_display'] = getCurrency($single_addons[$addon_index]['subtotal_with_discount'], $currencies[$currency]). ' ' . $currencies[$currency];
                $single_addons[$addon_index]['quantity_discount'] = round($quantity_discount, 2);
                $single_addons[$addon_index]['quantity_discount_display'] = getCurrency($quantity_discount, $currencies[$currency]). ' ' . $currencies[$currency];
                $single_addons[$addon_index]['grand_total'] = round(($single_addons[$addon_index]['subtotal_with_discount'] - $single_addons[$addon_index]['quantity_discount']), 2);
                $single_addons[$addon_index]['grand_total_display'] = getCurrency(round(($single_addons[$addon_index]['subtotal_with_discount'] - $single_addons[$addon_index]['quantity_discount']), 2), $currencies[$currency]). ' ' . $currencies[$currency];
                $single_addons[$addon_index]['vat'] = $item->getModel()->vat;
                $single_addons[$addon_index]['item_number'] = $item->getBillingItem()->item_number;

                $single_addons[$addon_index]['link_data'] = EventsiteBillingItemRepository::itemLinkToWithDetail(['event_id' => $this->getOrderEventId(), 'language_id' => $this->getUtility()->getLangaugeId()], $item->getModel());
            
                if($item->getDiscountAmount() > 0) $show_display_discount_col = 1;
            }
        }

        return ['group_addons' => $group_addons, 'single_addons' => $single_addons, 'show_display_discount_col' => $show_display_discount_col, 'show_display_discount_qty_col' => $show_display_discount_qty_col];
    }

    /**
     * @return [type]
     */
    private function _getAttendeeSummaryDetail()
    {
        $currencies = getCurrencyArray();

        $currency = $this->getPaymentSettingAttribute('eventsite_currency');

        $attendees = [];
        
        foreach ($this->getAttendees() as $attendee) {

            $attendee_info = $attendee->getModel()->toArray();

            $sub_total = 0;

            $group_ids = [];

            $group_addons = [];

            $single_addons = [];

            foreach ($attendee->getItems() as $key => $item) {
                if ($item->getModel()->group_id) {
                    if (!in_array($item->getModel()->group_id, $group_ids)) {
                        $group_ids[] = $item->getModel()->group_id;
                        $group_index = array_search($item->getModel()->group_id, $group_ids);
                        $addon_group_detail = $item->getModel()->addon_group_detail()->with(['info' => function ($query) {
                            return $query->where('languages_id', '=', $this->getUtility()->getLangaugeId());
                        }])->first()->toArray();
                        $group_addons[$group_index]['group_id'] = $item->getModel()->group_id;
                        $group_addons[$group_index]['group_name'] = isset($addon_group_detail['info'][0]['value']) ? $addon_group_detail['info'][0]['value'] : '';
                        $group_addons[$group_index]['addons'] = [];
                    }
                    $group_index = array_search($item->getModel()->group_id, $group_ids);
                    $group_addons[$group_index]['addons'][] = [
                        'addon_id' => $item->getModel()->addon_id,
                        'name' => $item->getItemName(),
                        'description' => $item->getBillingItem()->info()->where('name', '=', 'description')->value('value'),
                        'price' => round($item->getPriceUnit(), 2),
                        'discount' => round($item->getDiscountAmount(), 2),
                        'qty' => $item->getQuantity(),
                        'subtotal' => round($item->getPriceSubtotal(), 2),
                        'subtotal_with_discount' => round($item->getPriceSubtotalWithDiscount(), 2),
                    ];

                    //Attendee wise sub total
                    $sub_total += round($item->getPriceSubtotalWithDiscount(), 2);
                } else {
                    $single_addons[] = [
                        'addon_id' => $item->getModel()->addon_id,
                        'name' => $item->getItemName(),
                        'description' => $item->getBillingItem()->info()->where('name', '=', 'description')->value('value'),
                        'price' => round($item->getPriceUnit(), 2),
                        'discount' => round($item->getDiscountAmount(), 2),
                        'qty' => $item->getQuantity(),
                        'subtotal' => round($item->getPriceSubtotal(), 2),
                        'subtotal_with_discount' => round($item->getPriceSubtotalWithDiscount(), 2),
                    ];

                    //Attendee wise sub total
                    $sub_total += round($item->getPriceSubtotalWithDiscount(), 2);
                }
            }

            $order_attendee = $attendee->getOrderAttendee();

            $sections = $attendee->getFields($order_attendee->registration_form_id, true);
            
            $payment_form_setting = $this->_getPaymentFormSetting($order_attendee->registration_form_id, true);

            $custom_fields = \App\Eventbuizz\Repositories\AttendeeRepository::getCustomFieldsAnswers($attendee_info, ['event_id' => $this->getOrderEventId(), 'language_id' => $this->getUtility()->getLangaugeId(), 'registration_form_id' => $order_attendee->registration_form_id]);
            
            $documents = $this->documents($attendee->getModel()->id);

            $attendees[] = ['attendee_info' => $attendee_info, 'sub_total' => getCurrency($sub_total, $currencies[$currency]). ' ' . $currencies[$currency], 'addons' => ['group_addons' => $group_addons, 'single_addons' => $single_addons], 'order_attendee' => $order_attendee, 'sections' => $sections, 'hotels' => $attendee->_getAttendeeHotels(true), 'payment_form_setting' => $payment_form_setting, 'custom_fields' => $custom_fields, 'documents' => $documents];
        }

        return $attendees;
    }

    /**
     * @return [type]
     */
    private function documents($attendee_id)
    {
        $order_id = $this->getModel()->id;

        $docs = \App\Models\EventsiteDocumentResult::where('order_id', $order_id)->where('attendee_id', $attendee_id)->with(['types'=> function($q){ return $q->select(['conf_eventsite_document_types.id as value', 'conf_eventsite_document_types.name as label'])->whereNull('conf_document_result_document_type.deleted_at'); }])->orderBy('id', 'desc')->get()->toArray();
        if (in_array(config('app.env'), ["production"])) {
            foreach ($docs as $key => $doc) {
                $docs[$key]['s3'] = 1;
                $docs[$key]['s3_url'] = getS3Image('_eventsite_assets/documents/clients/' . $doc['path']);
            }
        }

		return $docs;
    }

    /**
     * @return [type]
     */
    public function saveOrderLogs($data)
    {
        \App\Models\OrderRequestLog::create([
            'url' => url()->full(),
            'request' => $data,
            'event_id' => $this->_utility->getEventId(),
            'order_id' => $this->getModelAttribute('id')
        ]);
    }
    
    /**
     * removeAllAttendeeOrderData
     *
     * @param  mixed $order_id
     * @param  mixed $attendee_id
     * @return void
     */
    public function removeAllAttendeeOrderData($order_id, $attendee_id)
    {
        //Order Attendee
        \App\Models\BillingOrderAttendee::where('order_id', $order_id)->where('attendee_id', $attendee_id)->update([
            'status' => 'incomplete'
        ]);

        //Addons
        \App\Models\BillingOrderAddon::where('order_id', $order_id)->where('attendee_id', $attendee_id)->delete();

        //Keywords
        \App\Models\EventOrderKeyword::where('order_id', $order_id)->where('attendee_id', $attendee_id)->delete();

        //Sub registrations
        \App\Models\EventOrderSubRegistrationAnswer::where('order_id', $order_id)->where('attendee_id', $attendee_id)->delete();

        //Attendee billing
        //This should not work for cancel order now
        if($this->getUtility()->isDraft()) { // During cancel order if main attendee removed then update attendee billing with new main attendee so don,t delete it
            $billing_info = \App\Models\AttendeeBilling::where('attendee_id', $attendee_id)->where('event_id', $this->getOrderEventId())->where('order_id', $order_id)->first();
        }

        if($billing_info) {
            $billing_info->delete();
            \App\Models\AttendeeBilling::create([
                'attendee_id' => $attendee_id,
                'event_id' => $this->getOrderEventId(),
                'order_id' => $order_id
            ]);
        }

        //Hotels
        foreach ($this->getHotel() as $key => $hotel) {
            if($hotel->getModel()->attendee_id == $attendee_id) {
                $this->_removed_hotels[] = $hotel->getModel()->id;
                unset($this->_hotel[$key]);
            }
        } 

        \App\Models\EventHotelPerson::where('order_id', $order_id)->where('attendee_id', $attendee_id)->delete();
    }

    /**
     * updateAllAttendeeOrderData
     *
     * @param  mixed $order_id
     * @param  mixed $old_attendee_id
     * @param  mixed $new_attendee_id
     * @return void
     */
    public function updateAllAttendeeOrderData($order_id, $old_attendee_id, $new_attendee_id)
    {
        //Addons
        \App\Models\BillingOrderAddon::where('order_id', $order_id)->where('attendee_id', $old_attendee_id)->update([
            'attendee_id' => $new_attendee_id
        ]);

        //Keywords
        \App\Models\EventOrderKeyword::where('order_id', $order_id)->where('attendee_id', $old_attendee_id)->update([
            'attendee_id' => $new_attendee_id
        ]);

        //Sub registrations
        \App\Models\EventOrderSubRegistrationAnswer::where('order_id', $order_id)->where('attendee_id', $old_attendee_id)->update([
            'attendee_id' => $new_attendee_id
        ]);

        //Attendee billing
        $billing_info = \App\Models\AttendeeBilling::where('attendee_id', $old_attendee_id)->where('event_id', $this->getOrderEventId())->where('order_id', $order_id)->first();

        if($billing_info) {
            $billing_info->delete();
            \App\Models\AttendeeBilling::create([
                'attendee_id' => $old_attendee_id,
                'event_id' => $this->getOrderEventId(),
                'order_id' => $order_id
            ]);
        }

        //Hotels
        \App\Models\EventOrderHotel::where('attendee_id', $old_attendee_id)->where('order_id', $order_id)->update([
            'attendee_id' => $new_attendee_id
        ]);
        \App\Models\EventHotelPerson::where('attendee_id', $old_attendee_id)->where('order_hotel_id', $order_hotel->id)->update([
            'attendee_id' => $new_attendee_id
        ]);
    }

    /**
     * cloneOrder
     *
     * @param  mixed $order_id
     * @param  mixed $attendee_id
     * @return void
     */
    public function cloneOrder($order_id, $is_credit_note = null, $platform = null)
    {
        $order = \App\Models\BillingOrder::where('id', $order_id)->first();

        if($order) {

            $clone_order = $order->replicate();
            $clone_order->clone_of = $order->id;
            if($platform) {
                $clone_order->platform = $platform;
            } else {
                $clone_order->platform = null;
            }
            $clone_order->parent_id = $order->parent_id ? $order->parent_id : $order->id;
            if($is_credit_note) {
                $clone_order->order_number = $this->_generateOrderNumber();
                $clone_order->is_credit_note = 1;
            } else {
                $clone_order->status = "draft";
            }
            $clone_order->save();

            //Attendees
            $order_attendees = \App\Models\BillingOrderAttendee::where('order_id', $order->id)->get();
            if(count($order_attendees) > 0) {
                foreach($order_attendees as $order_attendee) {
                    $clone_order_attendee = $order_attendee->replicate();
                    $clone_order_attendee->order_id = $clone_order->id;
                    $clone_order_attendee->save();
                }
            }

            //Attendee billing
            $attendee_billing = \App\Models\AttendeeBilling::where('order_id', $order->id)->where('attendee_id', $order->attendee_id)->first();
            if($attendee_billing) {
                $clone_attendee_billing = $attendee_billing->replicate();
                $clone_attendee_billing->order_id = $clone_order->id;
                $clone_attendee_billing->save();
            }

            //Items
            $order_items = \App\Models\BillingOrderAddon::where('order_id', $order->id)->get();
            if(count($order_items) > 0) {
                foreach($order_items as $order_item) {
                    $clone_order_item = $order_item->replicate();
                    $clone_order_item->order_id = $clone_order->id;
                    $clone_order_item->save();
                }
            }

            //Order rules
            $rule_logs = \App\Models\BillingOrderRuleLog::where('order_id', $order->id)->get();
            if(count($rule_logs) > 0) {
                foreach($rule_logs as $rule_log) {
                    $clone_rule_log = $rule_log->replicate();
                    $clone_rule_log->order_id = $clone_order->id;
                    $clone_rule_log->save();
                }
            }

            //Order vats
            $rule_vats = \App\Models\BillingOrderVAT::where('order_id', $order->id)->get();
            if(count($rule_vats) > 0) {
                foreach($rule_vats as $rule_vat) {
                    $clone_rule_vat = $rule_vat->replicate();
                    $clone_rule_vat->order_id = $clone_order->id;
                    $clone_rule_vat->save();
                }
            }

            //Order hotels
            $order_hotels = \App\Models\EventOrderHotel::where('order_id', $order->id)->get();
            
            if(count($order_hotels) > 0) {

                foreach($order_hotels as $order_hotel) {

                    $clone_order_hotel = $order_hotel->replicate();
                    $clone_order_hotel->order_id = $clone_order->id;
                    $clone_order_hotel->save();

                    //Order hotel rooms
                    $order_hotel_rooms = \App\Models\EventOrderHotelRoom::where('order_id', $order->id)->where('order_hotel_id', $order_hotel->id)->get();
                    if(count($order_hotel_rooms) > 0) {
                        foreach($order_hotel_rooms as $order_hotel_room) {
                            $clone_order_hotel_room = $order_hotel_room->replicate();
                            $clone_order_hotel_room->order_id = $clone_order->id;
                            $clone_order_hotel_room->order_hotel_id = $clone_order_hotel->id;
                            $clone_order_hotel_room->save();
                        }
                    }

                    //Order hotel persons
                    $order_hotel_persons = \App\Models\EventHotelPerson::where('order_id', $order->id)->where('order_hotel_id', $order_hotel->id)->get();

                    if(count($order_hotel_persons) > 0) {
                        foreach($order_hotel_persons as $order_hotel_person) {
                            $clone_order_hotel_person = $order_hotel_person->replicate();
                            $clone_order_hotel_person->order_id = $clone_order->id;
                            $clone_order_hotel_person->order_hotel_id = $clone_order_hotel->id;
                            $clone_order_hotel_person->save();
                        }
                    }
                }
            }

            // Sub registrations
            $sub_registration_results = \App\Models\EventOrderSubRegistrationAnswer::where('order_id', $order->id)->get();

            if(count($sub_registration_results) > 0) {
                foreach($sub_registration_results as $sub_registration_result) {
                    $clone_sub_registration_result = $sub_registration_result->replicate();
                    $clone_sub_registration_result->order_id = $clone_order->id;
                    $clone_sub_registration_result->save();
                }
            }

            // Keywords
            $keyword_results = \App\Models\EventOrderKeyword::where('order_id', $order->id)->get();

            if(count($keyword_results) > 0) {
                foreach($keyword_results as $keyword_result) {
                    $clone_keyword_result = $keyword_result->replicate();
                    $clone_keyword_result->order_id = $clone_order->id;
                    $clone_keyword_result->save();
                }
            }
            
            // Keywords
            $document_results = \App\Models\EventsiteDocumentResult::where('order_id', $order->id)->get();

            if(count($document_results) > 0) {
                foreach($document_results as $document_result) {
                    $clone_document_result = $document_result->replicate();
                    $clone_document_result->order_id = $clone_order->id;
                    $clone_document_result->save();

                    $document_result_attached_types = \App\Models\EventsiteDocumentResultDocumentType::where('document_result_id', $document_result->id)->get();
                    if(count($document_result_attached_types) > 0) {
                        foreach($document_result_attached_types as $document_result_attached_type) {
                            $clone_document_result_attached_types = $document_result_attached_type->replicate();
                            $clone_document_result_attached_types->document_result_id = $clone_document_result->id;
                            $clone_document_result_attached_types->save();
                        }
                    }
                }
            }

            $order_form_builder_results = \App\Models\FormBuilderFormResult::where('order_id', $order_id)->get();

            if(count($order_form_builder_results)){
                foreach($order_form_builder_results as $result) {
                    $clone_form_builder_result = $result->replicate();
                    $clone_form_builder_result->order_id = $clone_order->id;
                    $clone_form_builder_result->save();
                }
            }

            return $clone_order;
        }
    }
        
    /**
     * loadTicketsIds
     *
     * @return void
     */
    public function loadTicketsIds()
    {
        $order_id = $this->getModel()->id;

        $addons = \App\Models\BillingOrderAddon::where('order_id', $order_id)->pluck('id')->toArray();

        if(count($addons) > 0) {
            $this->_generated_tickets_ids = \App\Models\EventTicket::whereIn('addon_id', $addons)->where('event_id', $this->getOrderEventId())->pluck('id')->toArray();
        } else {
            $this->_generated_tickets_ids = [];
        }

    }

    /**
     * verifyAttendees
     *
     * @return void
     */
    public function verifyAttendees()
    {
        $count = 0;

        foreach ($this->getAllAttendees() as $attendee) {
            
            $event_attendee = \App\Models\EventAttendee::where('attendee_id', $attendee->getModel()->id)
                    ->where('event_id', $this->getOrderEventId())
                    ->whereNull('deleted_at')
                    ->orderBy('id', 'desc')
                    ->first();

            if(!$event_attendee) {
                $count = $count + 1;
            }
            
        }

        if(count($this->getAllAttendees()) == $count) {
            return true;
        }

        return false;
    }

}
