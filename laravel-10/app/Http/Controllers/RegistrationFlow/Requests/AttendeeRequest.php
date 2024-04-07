<?php

namespace App\Http\Controllers\RegistrationFlow\Requests;

use App\Eventbuizz\Repositories\EventSiteRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Phone;
use App\Eventbuizz\Repositories\AttendeeRepository;

class AttendeeRequest extends FormRequest
{
    protected $rules = [];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $input = $this->request->all();
        $method = $this->method();
        if (null !== $this->get('_method', null)) {
            $method = $this->get('_method');
        }
        $this->offsetUnset('_method');
        switch ($method) {
            case 'DELETE':
            case 'GET':
                break;
            case 'POST':
                foreach ((array) $this->request->get('sections') as $section) {
                    if(!in_array($section['field_alias'], ["company_detail", "po_number"])) {
                        foreach ($section['fields'] as $field) {
                            if ($field['mandatory'] == 1 && in_array($field['field_alias'], ["password"]) && !request()->attendee_id) {
                                $this->rules[$field['field_alias']] = 'bail|required|min:6';
                                $this->rules['confirm_password'] = 'bail|required_with:password|same:password|min:6';
                            } else if ($field['mandatory'] == 1 && in_array($field['field_alias'], ["email", "confirm_email"])) {
                                $this->rules[$field['field_alias']] = 'bail|email';
                                if(!request()->attendee_id) {
                                    $this->rules['confirm_email'] = 'bail|required_with:email|same:email';
                                }
                            } else if ($field['mandatory'] == 1 && in_array($field['field_alias'], ["date_of_issue_passport", "date_of_expiry_passport", "EMPLOYMENT_DATE", "BIRTHDAY_YEAR"])) {
                                $this->rules[$field['field_alias']] = 'bail|date_format:Y-m-d';
                            } else if ($field['mandatory'] == 1 && in_array($field['field_alias'], ["age"])) {
                                $this->rules[$field['field_alias']] = 'bail|integer';
                            } else if ((isset($input[$field['field_alias']]) && $input[$field['field_alias']]) && ($field['mandatory'] == 1 || $input[$field['field_alias']]) && in_array($field['field_alias'], ["phone"])) {
                                $this->rules[$field['field_alias']] = [
                                    'bail',
                                    'required',
                                    new Phone()
                                ];
                            } else if ($field['mandatory'] == 1 && !in_array($field['field_alias'], ["custom_field_id", "credit_card_payment", "company_invoice_payment", "company_public_payment", "password", "confirm_password", "attendee_type", "phone"])) {
                                $this->rules[$field['field_alias']] = 'bail|required';
                            }
                        }
                    }
                }
                break;
            case 'PUT':
            case 'PATCH':
                break;
            default:
                break;
        }

        return $this->rules;
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if(request()->isMethod('POST')) {

            if($this->request->get('registration_form_id')) {

                $input = request()->all();

                $labels = request()->event['labels'];

                $eventsite_setting = request()->event['eventsite_setting'];

                $eventsite_form_setting = \App\Models\EventsiteSetting::where('event_id', '=', request()->event_id)->where('registration_form_id', $this->request->get('registration_form_id'))->first();

                $validate_response = $this->validateWaitingAndStock($labels, $eventsite_form_setting, $eventsite_setting);

                if(!$validate_response['success']) {
                    $validator->after(function ($validator) use ($validate_response) {
                        $validator->errors()->add('general_error', $validate_response['message']);
                    });
                }

                $attendee_setting = request()->event['attendee_setting'];

                $attendee = \App\Models\Attendee::where('email', request()->email)->where('organizer_id', request()->organizer_id)->first();

                if ($attendee) {
                    $count = \App\Models\EventAttendee::where('attendee_id', $attendee->id)->where('event_id', request()->event_id)->count();
                    if ($count > 0 && in_array(request()->provider, ['attendee', 'embed'])) {
                        $validator->after(function ($validator) use ($labels) {
                            $validator->errors()->add('email', $labels['EMAIL_ALREADY_EXISTS']);
                        });
                    } else if(request()->order_id) {
                        $order_attendee = \App\Models\BillingOrderAttendee::where('order_id', request()->order_id)->where('attendee_id',$attendee->id)->first();
                        if($order_attendee && (($order_attendee->attendee_id == $attendee->id && !request()->attendee_id) || ($order_attendee->attendee_id != request()->attendee_id && request()->attendee_id))) { 
                            $validator->after(function ($validator) use ($labels) {
                                $validator->errors()->add('email', $labels['EMAIL_ALREADY_EXISTS']);
                            });
                        } else {
                            $active_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  request()->event_id, 'status' => ['completed']], false, true);
                            if(!empty($active_orders_ids)) {
                                $order_attendee = \App\Models\BillingOrderAttendee::whereIn('order_id', $active_orders_ids)->where('order_id', '!=',request()->order_id)->where('attendee_id',$attendee->id)->first();
                                if($order_attendee) {
                                    $validator->after(function ($validator) use ($labels) {
                                        $validator->errors()->add('email', $labels['EMAIL_ALREADY_EXISTS']);
                                    });
                                }
                            }
                        }
                    } else {
                        if (isset($validate_response['is_waiting']) && $validate_response['is_waiting'] == 1) {
                            $waiting_list_attendee = \App\Models\WaitingListAttendee::where('attendee_id', $attendee->id)->where('event_id', request()->event_id)->where('status', '!=', 3)->first();
                            if ($waiting_list_attendee) {
                                $validator->after(function ($validator) use ($labels) {
                                    $validator->errors()->add('email', $labels['EMAIL_ALREADY_EXISTS']);
                                });
                            }
                        }
                    }
                }
        
                $validate_attendee_response = AttendeeRepository::validateAttendeeRegistration($attendee_setting, $labels, request()->organizer_id, request()->event_id, request()->email, "email");

                if(!$validate_attendee_response['success']) {
                    $validator->after(function ($validator) use ($validate_attendee_response) {
                        $validator->errors()->add($validate_attendee_response['validate_index'], $validate_attendee_response['label']);
                    });
                }
                
                //Registration Date
                if ($eventsite_form_setting['registration_end_date'] != '0000-00-00 00:00:00') {
                    $currentDate = strtotime(\Carbon\Carbon::now());
                    $startDate = strtotime(\Carbon\Carbon::parse($eventsite_form_setting['registration_end_date'])->toDateString().' '.$eventsite_form_setting['registration_end_time']);
                    if ($currentDate > $startDate) {
                        $validator->after(function ($validator) use ($labels) {
                            $validator->errors()->add('general_error', $labels['REGISTER_DATE_END']);
                        });
                    }
                }

            } else {
                $validator->after(function ($validator) use ($labels) {
                    $validator->errors()->add('general_error', 'You have not any default registration form is enabled');
                });
            }
              
        }
    }

    public function messages()
    {
        $message = array();

        $labels = request()->event['labels'];

        foreach ((array) $this->request->get('sections') as $section) {
            foreach ($section['fields'] as $field) {
                if (!in_array($field['field_alias'], ["custom_field_id", "credit_card_payment", "company_invoice_payment", "company_public_payment"])) {
                    $message[$field['field_alias'] . '.required'] =  $labels['REGISTRATION_FORM_FIELD_REQUIRED'];
                }
            }
        }

        foreach ((array) $this->request->get('custom_fields') as $custom_field) {
            $message['custom_field_' . $custom_field['id'] . '.required'] =  $labels['REGISTRATION_FORM_FIELD_REQUIRED'];
        }

        $message['email.email'] = $labels['REGISTRATION_ERROR_EMAIL'];
        $message['confirm_email.same'] = $labels['REGISTRATION_FORM_CONFIRM_EMAIL_MATCH'];
        $message['password.min'] = $labels['REGISTRATION_FORM_MIN_PASSWORD'];
        $message['confirm_password.same'] = $labels['REGISTRATION_FORM_CONFIRM_PASSWORD'];
        $message['confirm_password.min'] = $labels['REGISTRATION_FORM_MIN_PASSWORD'];
        $message['age.integer'] = $labels['REGISTRATION_FORM_AGE_MUST_BE_INTEGER'];

        return $message;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if(request()->route('order_id') && request()->route('attendee_id')) {
            $order_attendee = \App\Models\BillingOrderAttendee::where('order_id', request()->route('order_id'))->where('attendee_id', request()->route('attendee_id'))->first();
            $registration_form = (object)EventSiteSettingRepository::getRegistrationForm(["event_id" => request()->event_id, 'type_id' => $order_attendee->attendee_type]);
            $registration_form_id = $registration_form ? ($registration_form->id != request()->registration_form_id ? request()->registration_form_id : $registration_form->id) : 0;
        } else {
            $registration_form_id = (int) request()->registration_form_id;
        }

        $sections = EventSiteSettingRepository::getAllSections(["event_id" => request()->event_id, "language_id" => request()->language_id, "status" => 1, 'registration_form_id' => $registration_form_id]);

        $custom_fields = EventSiteRepository::getCustomFields(request()->all());

        if (strpos($this->phone, '+') === false && $this->phone) {
            $phone = $this->calling_code_phone . '-' . $this->phone;
        } else if ($this->phone) {
            $phone = $this->phone;
        } else {
            $phone = "";
        }

        $this->merge([
            'sections' => $sections,
            'custom_fields' => $custom_fields,
            'phone' => $phone,
            'registration_form_id' => $registration_form_id,
        ]);
    }
    
    /**
     * validateWaitingAndStock
     *
     * @param  mixed $labels
     * @param  mixed $eventsite_form_setting
     * @param  mixed $eventsite_setting
     * @return void
     */
    protected function validateWaitingAndStock($labels, $eventsite_form_setting, $eventsite_setting)
    {
        $active_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  request()->event_id, 'status' => ['draft', 'completed']], false, true);

        //Validate form stock
        $total = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => $this->request->get('registration_form_id')], true);
            
        $total = request()->order_id && request()->route('attendee_id') ? $total : ($total + 1);

        //Validate global stock
        $global_total = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => 0], true);

        $global_total = request()->order_id && request()->route('attendee_id') ? $global_total : ($global_total + 1);

        $registration_form_id = (int) request()->registration_form_id;

        $waiting_list_setting = EventSiteSettingRepository::getWaitingListSetting(['event_id' => request()->event_id, 'registration_form_id' => $registration_form_id]);

        // If order is already created and new attendee created
        if(request()->route('order_id') && !request()->route('attendee_id')) {

            $order_attendee = \App\Models\BillingOrder::where('conf_billing_orders.id', request()->route('order_id'))->where('conf_billing_order_attendees.order_id', request()->route('order_id'))->join('conf_billing_order_attendees', 'conf_billing_order_attendees.attendee_id', '=', 'conf_billing_orders.attendee_id')->select('conf_billing_order_attendees.attendee_type', 'conf_billing_orders.is_waitinglist')->first();
            
            if($order_attendee->is_waitinglist == 1) {

                $main_attendee_registration_form = (object)EventSiteSettingRepository::getRegistrationForm(["event_id" => request()->event_id, 'type_id' => $order_attendee->attendee_type]);
                
                $main_attendee_registration_form_id = $main_attendee_registration_form->id;
                
                if($main_attendee_registration_form_id != $registration_form_id) {
                    return [
                        'success' => false,
                        'message' => $labels['REGISTRATION_FORM_USE_SAME_ATTENDEE_TYPE']
                    ];
                }

            } else {
                if($waiting_list_setting->status == 1 || ($waiting_list_setting->after_stocked_to_waitinglist == 1 && (((int)$eventsite_form_setting->ticket_left > 0 && $total > (int)$eventsite_form_setting->ticket_left) || ((int)$eventsite_setting->ticket_left > 0 && $global_total > (int)$eventsite_setting->ticket_left)))) {
                    return [
                        'success' => false,
                        'message' => $labels['REGISTRATION_FORM_USE_SAME_ATTENDEE_TYPE']
                    ];
                }
            }

        }

        // If order is already created and attendee updated cases [Attendee type changes]
        if(request()->route('order_id') && request()->route('attendee_id')) {

            $order_attendee = \App\Models\BillingOrder::where('conf_billing_orders.id', request()->route('order_id'))->where('conf_billing_order_attendees.order_id', request()->route('order_id'))->join('conf_billing_order_attendees', 'conf_billing_order_attendees.attendee_id', '=', 'conf_billing_orders.attendee_id')->select('conf_billing_order_attendees.attendee_type', 'conf_billing_orders.is_waitinglist')->first();

            if($order_attendee->is_waitinglist == 0 && ($waiting_list_setting->status == 1 || ($waiting_list_setting->after_stocked_to_waitinglist == 1 && (((int)$eventsite_form_setting->ticket_left > 0 && $total > (int)$eventsite_form_setting->ticket_left) || ((int)$eventsite_setting->ticket_left > 0 && $global_total > (int)$eventsite_setting->ticket_left))))) {
                return [
                    'success' => false,
                    'message' => $labels['REGISTRATION_FORM_USE_SAME_ATTENDEE_TYPE']
                ];
            }
            
        }
        
        if($waiting_list_setting->status == 1 || ($waiting_list_setting->after_stocked_to_waitinglist == 1 && (((int)$eventsite_form_setting->ticket_left > 0 && $total > (int)$eventsite_form_setting->ticket_left) || ((int)$eventsite_setting->ticket_left > 0 && $global_total > (int)$eventsite_setting->ticket_left)))) {

            request()->merge(['is_waiting' => 1]); // Merge request for use ahead of controller processing

            return [
                'is_waiting' => 1,
                'success' => true
            ];

        } else {

            if((int)$eventsite_form_setting->ticket_left > 0 && $total > (int)$eventsite_form_setting->ticket_left) {
                return [
                    'success' => false,
                    'message' => $labels['REGISTER_TICKET_END']
                ];
            }
            
    
            if((int)$eventsite_setting->ticket_left > 0 && $global_total > (int)$eventsite_setting->ticket_left) {
                return [
                    'success' => false,
                    'message' => $labels['REGISTER_TICKET_END']
                ];
            }

        }
        
        request()->merge(['is_waiting' => 0]); // Merge request for use ahead of controller processing

        return [
            'is_waiting' => 0,
            'success' => true
        ];
    }
}
