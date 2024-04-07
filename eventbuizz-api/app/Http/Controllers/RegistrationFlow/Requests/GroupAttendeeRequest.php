<?php

namespace App\Http\Controllers\RegistrationFlow\Requests;

use App\Eventbuizz\Repositories\EventSiteSettingRepository;

use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;

use Illuminate\Foundation\Http\FormRequest;

use App\Eventbuizz\Repositories\AttendeeRepository;

class GroupAttendeeRequest extends FormRequest
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
        $method = $this->method();
        if (null !== $this->get('_method', null)) {
            $method = $this->get('_method');
        }
        $this->offsetUnset('_method');
        switch ($method) {
            case 'DELETE':
            case 'GET':
                break;
            case 'PUT':
                break;
            case 'POST':
                break;
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
        if(request()->isMethod('PUT')) {

            $input = request()->all();

            $labels = request()->event['labels'];

            $eventsite_setting = request()->event['eventsite_setting'];

            $attendee_setting = request()->event['attendee_setting'];
            
            $waiting_list_attendees_count = 0;
            
            if($input['attendees'] && count($input['attendees']) > 0) {

                $emails = array_column($input['attendees'], 'email');

                if(has_dupes($emails)) {
                    $validator->after(function ($validator) use ($labels) {
                        $validator->errors()->add('duplicates', $labels['REGISTRATION_FORM_PLEASE_USE_DIFFERENT_EMAIL']);
                    }); 
                } else {
                    foreach($input['attendees'] as $key => $request) {

                        $registration_form = EventSiteSettingRepository::getRegistrationForm(['event_id' => request()->event_id, 'type_id' => $request['attendee_type']]);

                        if($registration_form) {

                            $eventsite_form_setting = \App\Models\EventsiteSetting::where('event_id', '=', request()->event_id)->where('registration_form_id', $registration_form->id)->first();

                            $validate_response = $this->validateWaitingAndStock($labels, $eventsite_form_setting, $eventsite_setting, $registration_form, count($input['attendees']));

                            if(!$validate_response['success']) {
                                $validator->after(function ($validator) use ($validate_response) {
                                    $validator->errors()->add('email', $validate_response['message']);
                                });
                            }

                            if (isset($validate_response['is_waiting']) && $validate_response['is_waiting'] == 1) {
                                $waiting_list_attendees_count = $waiting_list_attendees_count + 1;
                            }

                            $attendee = \App\Models\Attendee::where('email', $request['email'])->where('organizer_id', request()->organizer_id)->first();
                    
                            if ($attendee) {
                                $count = \App\Models\EventAttendee::where('attendee_id', $attendee->id)->where('event_id', request()->event_id)->count();
                                if ($count > 0) {
                                    $validator->after(function ($validator) use ($labels, $key) {
                                        $validator->errors()->add('email-'.$key, $labels['EMAIL_ALREADY_EXISTS']);
                                    });
                                } else {
                                    if (isset($validate_response['is_waiting']) && $validate_response['is_waiting'] == 1) {
                                        $waiting_list_attendee = \App\Models\WaitingListAttendee::where('attendee_id', $attendee->id)->where('event_id', request()->event_id)->where('status', '!=', 3)->first();
                                        if ($waiting_list_attendee) {
                                            $validator->after(function ($validator) use ($labels, $key) {
                                                $validator->errors()->add('email-'.$key, $labels['EMAIL_ALREADY_EXISTS']);
                                            });
                                        }
                                    }
                                }
                            }
                    
                            $validate_attendee_response = AttendeeRepository::validateAttendeeRegistration($attendee_setting, $labels, request()->organizer_id, request()->event_id, $request['email'], 'email-'.$key);

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
                                    $validator->after(function ($validator) use ($labels, $key) {
                                        $validator->errors()->add('email-'.$key, $labels['REGISTER_DATE_END']);
                                    });
                                }
                            }

                        } else {
                            $validator->after(function ($validator) use ($labels, $key) {
                                $validator->errors()->add('email-'.$key, 'You have not any default registration form is enabled');
                            }); 
                        }
                        
                    }

                    if($waiting_list_attendees_count > 0 && $waiting_list_attendees_count != count((array)$input['attendees'])) {
                        $validator->after(function ($validator) use ($labels) {
                            $validator->errors()->add('general_error', $labels['REGISTRATION_FORM_USE_SAME_ATTENDEE_TYPE']);
                        });
                    }

                    if($waiting_list_attendees_count > 0) {
                        if(count(array_filter((array)$input['attendees'], function($val) use($input) {
                            return $val['attendee_type'] == $input['attendees'][0]['attendee_type'];
                        })) != count((array)$input['attendees'])) { 
                            $validator->after(function ($validator) use ($labels, $key) {
                                $validator->errors()->add('general_error', $labels['REGISTRATION_FORM_USE_SAME_ATTENDEE_TYPE']);
                            });
                        } else {
                            request()->merge(['is_waiting' => 1]); // Merge request for use ahead of controller processing
                        }
                    } else {
                        request()->merge(['is_waiting' => 0]); // Merge request for use ahead of controller processing
                    }
                }

            } else {
                $validator->after(function ($validator) use ($labels) {
                    $validator->errors()->add('general_error', "Please add some attendees");
                });
            }
        }
    }
        
    /**
     * validateWaitingAndStock
     *
     * @param  mixed $labels
     * @param  mixed $eventsite_form_setting
     * @param  mixed $eventsite_setting
     * @param  mixed $registration_form
     * @param  mixed $total_attendees
     * @return void
     */
    protected function validateWaitingAndStock($labels, $eventsite_form_setting, $eventsite_setting, $registration_form, $total_attendees)
    {
        $active_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  request()->event_id, 'status' => ['draft', 'completed']], false, true);

        //Validate form stock
        $total = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => $registration_form->id], true);
            
        $total = $total + $total_attendees;

        //Validate global stock
        $global_total = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => 0], true);

        $global_total = $global_total + $total_attendees;

        $registration_form_id = (int) $registration_form->id;

        $waiting_list_setting = EventSiteSettingRepository::getWaitingListSetting(['event_id' => request()->event_id, 'registration_form_id' => $registration_form_id]);
        
        if($waiting_list_setting->status == 1 || ($waiting_list_setting->after_stocked_to_waitinglist == 1 && (((int)$eventsite_form_setting->ticket_left > 0 && $total > (int)$eventsite_form_setting->ticket_left) || ((int)$eventsite_setting->ticket_left > 0 && $global_total > (int)$eventsite_setting->ticket_left)))) {

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
        
        return [
            'is_waiting' => 0,
            'success' => true
        ];
    }
}