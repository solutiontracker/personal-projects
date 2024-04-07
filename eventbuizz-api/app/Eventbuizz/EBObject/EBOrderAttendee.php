<?php
namespace App\Eventbuizz\EBObject;

use Illuminate\Support\Facades\Hash;

use App\Events\RegistrationFlow\Event;

use App\Eventbuizz\Repositories\EventSiteSettingRepository;

class EBOrderAttendee
{
    protected $_model; // \App\Models\Attendee

    protected $_order_attendee_model; // \App\Models\BillingOrderAttendee

    protected $_event_attendee_model; // \App\Models\EventAttendee

    protected $_billing_fields; // \App\Models\AttendeeBilling

    protected $_fields; // \App\Models\BillingField

    protected $_order; // \App\Eventbuizz\EBObject\EBOrder

    protected $_is_main;

    protected $_first_name;

    protected $_last_name;

    protected $_info_fields;

    protected $_items_data; //Temporary holding variable

    protected $_items;

    protected $_verification_pending;

    const basic_fields_attendee = array('first_name', 'last_name', 'email', 'FIRST_NAME_PASSPORT', 'LAST_NAME_PASSPORT', 'BIRTHDAY_YEAR', 'SPOKEN_LANGUAGE', 'EMPLOYMENT_DATE', 'password', 'image', 'phone');

    const info_fields_array = array('delegate_number', 'table_number', 'age', 'gender', 'company_name', 'company_key', 'title', 'industry', 'about', 'phone', 'registration_type', 'country', 'organization', 'jobs', 'interests', 'allow_vote', 'allow_gallery', 'initial', 'department', 'custom_field_id', 'network_group', 'place_of_birth', 'passport_no', 'date_of_issue_passport', 'date_of_expiry_passport', 'private_street', 'private_house_number', 'private_post_code', 'private_city', 'private_country', 'private_street_2', 'private_state');

    const billing_fields = array('company_type', 'company_registration_number', 'ean', 'bruger_id', 'contact_person_name', 'contact_person_email', 'contact_person_mobile_number', 'company_street', 'company_house_number', 'company_post_code', 'company_city', 'company_country', 'poNumber', 'company_street_2', 'company_state', 'company_invoice_payer_company_name', 'company_invoice_payer_street_house_number', 'company_invoice_payer_post_code', 'company_invoice_payer_city', 'company_invoice_payer_country');

    const event_attendee_fields = array('attendee_type');

    private $infoKeys = array('delegate_number', 'table_number', 'age', 'gender', 'company_name', 'company_key', 'title', 'industry', 'about', 'phone', 'website', 'website_protocol', 'facebook', 'facebook_protocol', 'twitter', 'twitter_protocol', 'linkedin', 'linkedin_protocol', 'linkedin_profile_id', 'registration_type', 'country', 'organization', 'jobs', 'interests', 'initial', 'department', 'custom_field_id', 'network_group', 'billing_ref_attendee', 'billing_password', 'place_of_birth', 'passport_no', 'date_of_issue_passport', 'date_of_expiry_passport', 'private_house_number', 'private_street', 'private_street_2', 'private_post_code', 'private_city', 'private_country', 'private_state');

    function updateOrderObjectReference(EBOrder $order)
    {
        $this->_order = $order;
    }

    function __construct(EBOrder $order, $data, $items_data = null, $is_main = false, $is_edit = false)
    {
        $this->_order = $order;
        if ($order->isOrderPlaced() && $is_edit == false) {
            $attendee_id = $data;
            $this->_items_data = $order->getModel()->order_addons($attendee_id)->get();
            $this->_constructFromModel($attendee_id);
            $this->_is_main = ($this->_model->id == $this->_order->getModelAttribute('attendee_id'));
        } else {
            $this->_items_data = $items_data;
            $this->_constructFromInput($data);
            $this->_is_main = $is_main;
        }
        $this->_first_name = $this->_model->first_name;
        $this->_last_name = $this->_model->last_name;
    }

    function _constructFromModel($attendee_id)
    {
        $this->_model = \App\Models\Attendee::findOrFail($attendee_id);

        $this->_order_attendee_model = \App\Models\BillingOrderAttendee::where('order_id', $this->_order->getModelAttribute('id'))->where('attendee_id', $this->_model->id)->first();

        //It picking up the correct row from event attendees?? As we know there can be duplicate entries in this table. Need to verify this.
        $this->_event_attendee_model = $this->_model->event;

        $this->_info_fields = $this->_model->info;

        $this->_billing_fields = $this->_model->billingFields($this->getOrder()->getModelAttribute('id'), $this->getOrder()->getOrderEventId())->first();

        $this->_setupItems();
    }

    function _constructFromInput($data)
    {
        $this->_event_attendee_model = new \stdClass();

        $this->_model = new \App\Models\Attendee();

        $this->_model->organizer_id = $this->getOrder()->getUtility()->getOrganizerID();

        $this->_model->status = 1; //set as 1 by default;

        $this->_order_attendee_model = new \App\Models\BillingOrderAttendee();

        $this->_setupBasicFields($data);

        $this->_setupInfoFields($data);

        $this->_setupBillingFields($data);

        $this->_setupEventAttendeeFields($data);

        $this->_setupItems();
        
        $this->_setupOrderAttendeeFields($data);
    }

    function _setupOrderAttendeeFields($data = array())
    {
        $this->_order_attendee_model->subscriber_ids = $data['subscriber_ids'];
        $this->_order_attendee_model->accept_foods_allergies = $data['accept_foods_allergies'];
        $this->_order_attendee_model->accept_gdpr = $data['accept_gdpr'];
        $this->_order_attendee_model->cbkterms = $data['cbkterms'];
        $this->_order_attendee_model->member_number = $data['member_number'];
    }
    
    function _setupBasicFields($data = array())
    {
        //Basic fields adjustment
        foreach (self::basic_fields_attendee as $field) {
            if (array_key_exists($field, $data)) {
                if ($field == 'SPOKEN_LANGUAGE') {
                    $this->_model->{$field} = is_array($data[$field]) ? implode(',', $data[$field]) : $data[$field];
                } else if ($field == 'EMPLOYMENT_DATE' || $field == 'BIRTHDAY_YEAR') {
                    if (!empty($data[$field])) {
                        $this->_model->{$field} = \Carbon\Carbon::parse($data[$field])->format('Y-m-d');
                    }
                } else if ($field == 'password' && $data[$field] != '') {
                    $this->_model->{$field} = Hash::make($data[$field]);
                } else {
                    $this->_model->{$field} = $data[$field] ? $data[$field] : '';
                }
            }
        }
    }

    function _setupInfoFields($data = array())
    {
        if (count((array) $data['custom_field_id']) < 1 && count((array) $data['custom_fields']) > 0) {
            $data['custom_field_id'] = $data['custom_fields'];
        }

        $info_fields = $this->infoKeys;

        foreach ($info_fields as $field) {
            $field_data = [];
            if ($field == 'delegate_number' && isset($data['delegate'])) {
                $data['delegate_number'] = $data['delegate'];
            } else if (($field == 'date_of_issue_passport' || $field == 'date_of_expiry_passport') && isset($data[$field]) && !empty($data[$field])) {
                $data[$field] = \Carbon\Carbon::parse($data[$field])->format('Y-m-d');
            }
            if (array_key_exists($field, $data) && $data[$field] != '') {
                $field_data['name'] = $field;
                if ($field == 'custom_field_id') {
                    $field_data['name'] = $field . $this->getOrder()->getOrderEventId();
                    $field_data['value'] = is_array($data[$field]) ? implode(',', $data[$field]) : $data[$field];
                } else {
                    $field_data['value'] = $data[$field];
                }
                $field_data['status'] = 1;
                $field_data['languages_id'] = $this->getOrder()->getUtility()->getLangaugeId();
                $this->_info_fields[] = new \App\Models\AttendeeInfo($field_data);
            } else {
                if ($field == 'custom_field_id') {
                    $field = $field . $this->getOrder()->getOrderEventId();
                }
                $field_data['name'] = $field;
                $field_data['value'] = '';
                $field_data['status'] = 1;
                $field_data['languages_id'] = $this->getOrder()->getUtility()->getLangaugeId();
                $this->_info_fields[] = new \App\Models\AttendeeInfo($field_data);
            }
        }
    }

    function _setupBillingFields($data = array())
    {
        $fields = [];
        foreach (self::billing_fields as $field) {
            if (array_key_exists($field, $data)) {
                $fields['billing_' . $field] = $data[$field];
            }
        }
        $this->_billing_fields = new \App\Models\AttendeeBilling($fields);
    }

    function _setupEventAttendeeFields($data = array())
    {
        foreach (self::event_attendee_fields as $field) {
            if (array_key_exists($field, $data)) {
                $this->_event_attendee_model->{$field} = $data[$field];
            }
        }
    }

    function _setupItems()
    {
        if (count((array) $this->_items_data) < 1) {return false;}
        $type = ($this->_order->isOrderPlaced() && $this->_order->isEdit() == false) ? 'model' : 'input';
        foreach ($this->_items_data as $item_data) {
            $this->_items[] = new EBOrderItem($this, $item_data, $type);
        }
    }

    function _attachToOrder()
    {
        if (!$this->_model->id) {
            throw new \Exception('Attendee should be save before calling this method');
        }
        
        $attendee_type = $this->_event_attendee_model->attendee_type; 

        if($this->getOrder()->isEdit() && $this->_order_attendee_model->id) {
            $this->_order_attendee_model->save();
        } else {
            $this->_order_attendee_model->attendee_id = $this->_model->id;
            $this->_order_attendee_model->order_id = $this->_order->getModelAttribute('id');
            $this->_order_attendee_model->attendee_type = $attendee_type;
            $this->_order_attendee_model->registration_form_id = (int) $this->getRegistrationFormIdByAttendeeType($attendee_type);
            $this->_order_attendee_model->save();
        }
    }

    public function getRegistrationFormIdByAttendeeType($type_id)
    {
        $registration_form = (object)EventSiteSettingRepository::getRegistrationFormByAttendeeType(["event_id" => $this->getOrder()->getOrderEventId(), 'type_id' => $type_id]);
        return $registration_form ? $registration_form->id : 0;
    }

    public function getRegistrationFormIdByAttendee($type_id)
    {
        $registration_form = (object)EventSiteSettingRepository::getRegistrationFormByAttendeeType(["event_id" => $this->getOrder()->getOrderEventId(), 'type_id' => $type_id]);
        return $registration_form ? $registration_form : [];
    }

    public function getFields($registration_form_id, $load = false)
    {
        if(empty($this->_fields) && $load) {
            return $this->_fields = EventSiteSettingRepository::getAllSections(["event_id" => $this->getOrder()->getOrderEventId(), "language_id" => $this->getOrder()->getUtility()->getLangaugeId(), "status" => 1, 'registration_form_id' => $registration_form_id]);
        } else {
            return $this->_fields;
        }
    }

    public function getOrderAttendee()
    {
        return $this->_order_attendee_model;
    }
    
    function _saveItems()
    {
        foreach ($this->getItems() as $item) {
            // \Eventbuizz\EBObject\EBOrderItem
            $item->save();
        }
    }

    function _attachToEvent()
    {
        if ($this->getOrder()->getModelAttribute('is_waitinglist') == '1' || $this->_order->getUtility()->isDraft()) {
            return;
        }

        $is_active = '1';

        $status = '1';

        $verification_id = '';

        if ($this->getOrder()->getAttendeeSettingsAttribute('attendee_reg_verification') == '1' && in_array($this->getOrder()->getUtility()->getPanel(), ['attendee', 'embed'])) {
            $is_active = '0';
            $status = '0';
            $this->_verification_pending = true;
            $verification_id = md5($this->getOrder()->getOrderEventId() . $this->getOrder()->getUtility()->getOrganizerID() . "-" . time());
        }

        //Implement these fields 'email_sent', 'sms_sent', 'login_yet', 'speaker', 'sponser', 'exhibitor','default_language_id'
        if (!$this->_model->id) {
            throw new \Exception('Attendee should be saved before calling this method');
        }

        $previous_entry = \App\Models\EventAttendee::where('event_id', '=', $this->getOrder()->getOrderEventId())->where('attendee_id', '=', $this->_model->id)->whereNull('deleted_at')->first();

        if ($previous_entry instanceof \App\Models\EventAttendee) {
            //Do not do anything is this attendee is already attached to event.
            return;
        }

        $this->_event_attendee_model = new \App\Models\EventAttendee();
        $this->_event_attendee_model->attendee_id = $this->_model->id;
        $this->_event_attendee_model->event_id = $this->getOrder()->getOrderEventId();
        $this->_event_attendee_model->status = $status;
        $this->_event_attendee_model->gdpr = (int) $this->_order_attendee_model->accept_gdpr;
        $this->_event_attendee_model->accept_foods_allergies = (int) $this->_order_attendee_model->accept_foods_allergies;
        $this->_event_attendee_model->verification_id = $verification_id;
        $this->_event_attendee_model->is_active = $is_active;
        $this->_event_attendee_model->attendee_type = (int) $this->_order_attendee_model->attendee_type;
        $this->_event_attendee_model->default_language_id = $this->getOrder()->getUtility()->getLangaugeId(); // Clearify this whats its use
        $this->_event_attendee_model->save();
    }

    function save()
    {
        $email = $this->getModel()->email;

        $organizer_id = $this->getOrder()->getUtility()->getOrganizerID();

        $attendee = \App\Models\Attendee::where('email', '=', $email)->where('organizer_id', '=', $organizer_id)->first();
        
        $default_password = '123456';

        if ($this->getOrder()->getAttendeeSettingsAttribute('default_password')) {
            $default_password = $this->getOrder()->getAttendeeSettingsAttribute('default_password');
        }

        if ($attendee instanceof \App\Models\Attendee) //Existing attendee model
        {
            //Update first and last names if provided.
            $attendee->first_name = $this->_model->first_name ? $this->_model->first_name : $attendee->first_name;
            $attendee->last_name = $this->_model->last_name ? $this->_model->last_name : $attendee->last_name;
            $attendee->FIRST_NAME_PASSPORT = $this->_model->FIRST_NAME_PASSPORT ? $this->_model->FIRST_NAME_PASSPORT : $attendee->FIRST_NAME_PASSPORT;
            $attendee->LAST_NAME_PASSPORT = $this->_model->LAST_NAME_PASSPORT ? $this->_model->LAST_NAME_PASSPORT : $attendee->LAST_NAME_PASSPORT;
            $attendee->BIRTHDAY_YEAR = $this->_model->BIRTHDAY_YEAR ? $this->_model->BIRTHDAY_YEAR : $attendee->BIRTHDAY_YEAR;
            $attendee->SPOKEN_LANGUAGE = $this->_model->SPOKEN_LANGUAGE ? $this->_model->SPOKEN_LANGUAGE : $attendee->SPOKEN_LANGUAGE;
            $attendee->EMPLOYMENT_DATE = $this->_model->EMPLOYMENT_DATE ? $this->_model->EMPLOYMENT_DATE : $attendee->EMPLOYMENT_DATE;
            $attendee->phone = ($this->_model->phone != $attendee->phone && $this->_model->phone != '') ? $this->_model->phone : $attendee->phone;
            if ($this->_model->password) {
                $attendee->password = $this->_model->password;
                $attendee->change_password = 0;
            }

            //Email should be when update attendee
            if($attendee->email) {
                $attendee->save();
            }

            //Set the existing model as _model for this attendee
            $this->_model = $attendee; //Use existing attendee to avoid duplicates in attendees table.

        } else {
            if ($this->_model->password == '') {
                $this->_model->password = Hash::make($default_password);
            } else {
                $this->_model->change_password = 0;
            }

            //Email should be when update attendee
            if($this->_model->email) {
                $this->_model->save();
            }
        }

        if (count($this->_info_fields) > 0) {
            foreach ($this->_info_fields as $info) {
                // \App\Models\AttendeeInfo
                $values_array = array_merge($info->toArray(), ['attendee_id' => $this->getModel()->id]);
                $match_array = array_diff_key($values_array, array_flip(['status', 'value', 'created_at', 'updated_at', 'id']));
                if ($values_array['value'] == '') {
                    // Create empty record if it doesn't exist. If it does then the line below will affect nothing.
                    \App\Models\AttendeeInfo::firstOrCopyOrCreateEmpty($match_array, $values_array);
                } else {
                    \App\Models\AttendeeInfo::updateOrCreate($match_array, $values_array);
                }
            }
        }

        $this->_model->info; // Load model info in object
        
        if ($this->_billing_fields instanceof \App\Models\AttendeeBilling && $this->isMainAttendee()) {

            $this->_billing_fields->attendee_id = $this->_model->id;
            $this->_billing_fields->order_id = $this->getOrder()->getModelAttribute('id');
            $this->_billing_fields->event_id = $this->getOrder()->getOrderEventId();

            $match_array = [
                'attendee_id' => $this->_model->id,
                'order_id' => $this->getOrder()->getModelAttribute('id'),
                'event_id' => $this->getOrder()->getOrderEventId(),
            ];

            $values_array = $this->_billing_fields->toArray();
            
            \App\Models\AttendeeBilling::updateOrCreate($match_array, $values_array);
        }

        $this->_attachToOrder();

        //Attendee Registration Log entry


        $this->_saveItems();

        if(!$this->_order->getUtility()->isDraft()) {
            $this->_attachToEvent();
        }  
    }

    function _saveInfo()
    {
        $info_array = array();
        $langauge_id = $this->_order->getUtility()->getLangaugeId();
        foreach ($this->_info_fields as $name => $value) {
            $info_array[] = [
                'name' => $name,
                'value' => $value,
                'status' => 1,
                'languages_id' => $langauge_id,
            ];
        }
        if (count($info_array) > 0) {
            $this->_model->info()->createMany($info_array);
        }
    }

    function isMainAttendee()
    {
        return $this->_is_main;
    }

    function getInfo()
    {
        return $this->_info_fields;
    }

    function getOrder()
    {
        return $this->_order;
    }

    function getItems()
    {
        return $this->_items;
    }

    function getModel()
    {
        return $this->_model;
    }

    public function getEventAttendeeModel()
    {
        return $this->_event_attendee_model;
    }

    function isVerificationPending()
    {
        return (!($this->_event_attendee_model->is_active == '1' && $this->_event_attendee_model->verification_id == ''));
    }

    function toArray()
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

    function addItem($data)
    {
        $this->_items[] = new EBOrderItem($this, $data, 'input');
    }

    function replicateModel()
    {
        $this->_model = $this->getModel()->replicate();
    }

    function replicateInfoModels()
    {
        $info_fields = $this->_info_fields;
        unset($this->_info_fields);
        foreach ($info_fields as $field) {
            $this->_info_fields[] = $field->replicate();
        }
    }

    function replicateBillingModel()
    {
        if ($this->_billing_fields instanceof \App\Models\AttendeeBilling) {
            $this->_billing_fields->replicate();
        }
    }

    function getBillingModel()
    {
        return $this->_billing_fields;
    }

    function removeItem($item_id)
    {
        foreach ($this->_items as $index => $item) {
            if ($item->getBillingItemId() == $item_id) {
                unset($this->_items[$index]);
            }
        }
    }

    function updateInfo($input, $section)
    {
        if ($section == 'billing') {
            return $this->_billing_fields->update($input);
        } else if ($section == 'basic') {
            foreach ($input as $name => $value) {
                if ($name == 'SPOKEN_LANGUAGE') {
                    $input[$name] = implode(',', $value);
                } else if ($name == 'EMPLOYMENT_DATE' || $name == 'BIRTHDAY_YEAR') {
                    $input[$name] = $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '';
                }
            }
            return $this->_model->update($input);
        } else if ($section == 'info') {
            foreach ($input as $name => $value) {
                if ($name == 'delegate') //input adjustment
                {
                    $name = 'delegate_number';
                } else if ($name == 'custom_field_id' . $this->getOrder()->getOrderEventId()) {
                    $value = is_array($value) ? implode(',', $value) : $value;
                    $name = 'custom_field_id';
                } else if ($name == 'custom_field_id') {
                    $value = is_array($value) ? implode(',', $value) : $value;
                } else if (($name == 'date_of_issue_passport' || $name == 'date_of_expiry_passport') && isset($value) && !empty($value)) {
                    $value = $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '';
                }

                $info_fields = $this->infoKeys;

                if (in_array($name, $info_fields)) {
                    //since we are using updateOrCreate method here, we pass 2 params to it so one is used to match and find record in DB and the other is to update/create
                    if ($name == 'custom_field_id') {
                        $name = $name . $this->getOrder()->getOrderEventId();
                    }

                    $field_data['languages_id'] = $this->getOrder()->getUtility()->getLangaugeId();
                    $field_data['name'] = $name;
                    $field_data['attendee_id'] = $this->getModel()->id;

                    //To update, so now include the value so that the record could be updated/created
                    $field_data_save = $field_data;
                    $field_data_save['value'] = $value;
                    $field_data_save['status'] = 1;

                    \App\Models\AttendeeInfo::updateOrCreate($field_data, $field_data_save);
                }
            }
        } else if ($section == 'attendee_type') {

            // For parent attendee
            foreach ($input as $name => $value) {
                if ($name == 'attendee_type') {
                    $attendee_id = $this->getModel()->id;
                    $event_id = $this->getOrder()->getOrderEventId();
                    $attendee_type_data['attendee_type'] = $value;
                    \App\Models\EventAttendee::where('attendee_id', '=', $attendee_id)->where('event_id', '=', $event_id)->update($attendee_type_data);
                }
            }

            // For additional atteendees
            foreach ($input['attendee'] as $attendee_id => $additional) {
                $event_id = $this->getOrder()->getOrderEventId();
                $attendee_type_data['attendee_type'] = $additional['attendee_type_field'];
                \App\Models\EventAttendee::where('attendee_id', '=', $attendee_id)->where('event_id', '=', $event_id)->update($attendee_type_data);
            }
            
            //Attendee type [Just link to order attendee]
            $this->_order_attendee_model->attendee_type = $input['attendee_type'];
            $this->_order_attendee_model->registration_form_id = $this->getRegistrationFormIdByAttendeeType($input['attendee_type']);
            $this->_order_attendee_model->save();
            
        } else if ($section == 'order_attendee_fields') { 
            $this->_order_attendee_model->subscriber_ids = $input['subscriber_ids'];
            $this->_order_attendee_model->accept_foods_allergies = $input['accept_foods_allergies'];
            $this->_order_attendee_model->accept_gdpr = $input['accept_gdpr'];
            $this->_order_attendee_model->cbkterms = $input['cbkterms'];
            $this->_order_attendee_model->member_number = $input['member_number'];
            $this->_order_attendee_model->save();
        } else {
            return false;
        }
    }

    function getGdpr()
    {
        return $this->_event_attendee_model->gdpr;
    }

    public function getAttendeeType()
    {
        return $this->_order_attendee_model->attendee_type;
    }

    function setAsMainAttendee()
    {
        // This will only work if order is being edited and order is placed. Do not use this method outside EBObject classes.
        if ($this->getOrder()->isEdit()) {
            $this->_is_main = true;
        }
    }

    function updateAttendeeBillingWithNewMainAttendee()
    {
        $attendee_billing = \App\Models\AttendeeBilling::where('event_id', $this->getOrder()->getOrderEventId())->where('order_id', $this->getOrder()->getModelAttribute('id'))->first();
        if($attendee_billing) {
            $attendee_billing->attendee_id = $this->_model->id;
            $attendee_billing->save();
        } else {
            \App\Models\AttendeeBilling::create([
                'attendee_id' => $this->_model->id,
                'event_id' => $this->getOrder()->getOrderEventId(),
                'order_id' => $this->getOrder()->getModelAttribute('id')
            ]);
        }
    }

    function getBillingModelAttribute($name)
    {
        if (!$name) {
            return null;
        }

        return $this->_billing_fields->{$name};
    }

    function setBillingModelAttribute($data = array())
    {
        if ($this->getOrder()->isEdit()) {
            $this->_billing_fields = $data;
        }
    }

    function completeAttendeeIteration()
    {
        $order_attendee = $this->getOrderAttendee();

        if($order_attendee) {
            $order_attendee->status = 'complete';
            $order_attendee->save();
        }
    }

    public function _getAttendeeHotels($count = false)
    {
        $query = \App\Models\EventOrderHotel::where('order_id', $this->getOrder()->getModelAttribute('id'))->where('attendee_id', $this->getModel()->id);

        if($count) {
            return $query->count();
        } else {
            return $query->get();
        }
    }
}
