<?php

namespace App\Http\Controllers\Api\Requests\order;

use App\Eventbuizz\Repositories\EventSiteRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use Illuminate\Foundation\Http\FormRequest;
use App\Eventbuizz\Repositories\EventsiteBillingItemRepository;
use App\Eventbuizz\Repositories\HotelRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Eventbuizz\Repositories\AttendeeRepository;

class CreateOrder extends FormRequest
{
    protected $rules = [];

    /**
     * @param EventsiteBillingItemRepository $eventsiteBillingItemRepository
     * @param HotelRepository $HotelRepository
     */
    public function __construct(EventsiteBillingItemRepository $eventsiteBillingItemRepository, HotelRepository $hotelRepository)
    {
        $this->eventsiteBillingItemRepository = $eventsiteBillingItemRepository;
        $this->hotelRepository = $hotelRepository;
    }

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
            $labels = request()->event['labels'];
            
            $input = json_decode(request()->getContent(), true);

            $waiting_list_setting = request()->event['waiting_list_setting'];

            $eventsite_setting = request()->event['eventsite_setting'];

            $payment_setting = request()->event['payment_setting'];

            $attendee_setting = request()->event['attendee_setting'];

            $sections = EventSiteSettingRepository::getAllSections(["event_id" => request()->event_id, "language_id" => request()->language_id, "status" => 1]);

            $custom_fields = EventSiteRepository::getCustomFields(request()->all());

            //Registration items 
            request()->merge(["is_free" => ($eventsite_setting->payment_type == 0 ? 1 : 0), "rule" => true]);

            $items = $this->eventsiteBillingItemRepository->getRegistrationItems(request()->all());
            
            //Main attendee validation
            $this->validateAttendeeFields($validator, $waiting_list_setting, $attendee_setting, $eventsite_setting, $labels, $sections, $custom_fields, $input['mainAttendee'], 1);
            $this->validateAttendeeAddons($validator, $items, $labels, $input['addons'], 1);

            //Additional attendees validations
            if($payment_setting['evensite_additional_attendee'] == 1) {
                foreach($input['additional_attendees'] as $key => $attendee) {
                    $this->validateAttendeeFields($validator, $waiting_list_setting, $attendee_setting, $eventsite_setting, $labels, $sections, $custom_fields, $attendee, ($key + 2));
                    $this->validateAttendeeAddons($validator, $items, $labels, $attendee['addons'], ($key + 2));
                }
            }

            //Hotel validation
            if($payment_setting['show_hotels'] == 1) {
                $this->validateHotel($validator, $labels, $input);
            }

            //Payment information validation
            $this->validatePaymentInformation($validator, $labels, $input['payment_info'], $sections);

            //Voucher validation
            if($payment_setting['is_voucher']) {
                $this->validateVoucher($validator, $labels, $input);
            }

        }
    }
    
    /**
     * validateAttendeeFields
     *
     * @param  mixed $validator
     * @param  mixed $waiting_list_setting
     * @param  mixed $attendee_setting
     * @param  mixed $eventsite_setting
     * @param  mixed $labels
     * @param  mixed $sections
     * @param  mixed $custom_fields
     * @param  mixed $input
     * @param  mixed $n
     * @return void
     */
    public function validateAttendeeFields($validator, $waiting_list_setting, $attendee_setting, $eventsite_setting, $labels, $sections, $custom_fields, $input, $n) {
        foreach ((array) $sections as $section) {
            if(!in_array($section['field_alias'], ["company_detail", "po_number"])) {
                foreach ($section['fields'] as $field) {
                    if(in_array($field['field_alias'], ["password"]) && $input[$field['field_alias']]) {
                        $count = count(array_filter($section['fields'], function ($field) {
                            return in_array($field['field_alias'], ["password", "confirm_password"]) && $field['status'] == 1;
                        }));
                        if($count == 2) {
                            if(!Str::of($input['password'])->exactly($input['confirm_password'])) {
                                $validator->after(function ($validator) use ($labels, $field, $input, $n) {
                                    $validator->errors()->add("attendee-".$n."-field-".$field['field_alias'], "The password and confirm password must match.");
                                });
                            }
                        }
                    } else if(in_array($field['field_alias'], ["email"]) && $input[$field['field_alias']]) {
                        if(!validateEmail($input[$field['field_alias']])) {
                            $validator->after(function ($validator) use ($labels, $field, $input, $n) {
                                $validator->errors()->add("attendee-".$n."-field-".$field['field_alias'], "Invalid email!");
                            });
                        }
                    } else if(in_array($field['field_alias'], ["date_of_issue_passport", "date_of_expiry_passport", "EMPLOYMENT_DATE", "BIRTHDAY_YEAR"]) && $input[$field['field_alias']] && !validateDate($input[$field['field_alias']], 'd-m-Y')) { 
                        $validator->after(function ($validator) use ($labels, $field, $input, $n) {
                            $validator->errors()->add("attendee-".$n."-field-".$field['field_alias'], "Invalid date format.");
                        });
                    } else if($field['mandatory'] == 1 && $field['field_alias'] == "custom_field_id") {
                        //Custom field validation
                        foreach ((array) $custom_fields as $custom_field) {
                            if (!isset($input['custom_field_id'][$custom_field['id']]) || !$input['custom_field_id'][$custom_field['id']]) {
                                $validator->after(function ($validator) use ($labels, $field, $custom_field, $n) {
                                    $validator->errors()->add("attendee-".$n."-field-custom-".$custom_field['id'], $labels['REGISTRATION_FORM_FIELD_REQUIRED']);
                                });
                            }
                        }
                    } else {
                        if ($field['mandatory'] == 1 && !in_array($field['field_alias'], ["custom_field_id", "credit_card_payment", "company_invoice_payment", "company_public_payment"])) {
                            $validator->after(function ($validator) use ($labels, $field, $input, $n) {
                                if(!isset($input[$field['field_alias']]) || !$input[$field['field_alias']]) {
                                    $validator->errors()->add("attendee-".$n."-field-".$field['field_alias'], $labels['REGISTRATION_FORM_FIELD_REQUIRED']);
                                }
                            });
                        }
                    }
                }
            }
        }

        $attendee = \App\Models\Attendee::where('email', $input['email'])->where('organizer_id', request()->organizer_id)->first();
      
        if ($attendee) {
            $count = \App\Models\EventAttendee::where('attendee_id', $attendee->id)->where('event_id', request()->event_id)->count();
            if ($count > 0) {
                $validator->after(function ($validator) use ($labels, $n) {
                    $validator->errors()->add("attendee-".$n."-field-email", $labels['EMAIL_ALREADY_EXISTS']);
                });
            } else {
                if ($waiting_list_setting['status'] == '1') {
                    $waiting_list_attendee = \App\Models\WaitingListAttendee::where('attendee_id', $attendee->id)->where('event_id', request()->event_id)->where('status', '!=', 3)->first();
                    if ($waiting_list_attendee) {
                        $validator->after(function ($validator) use ($labels, $n) {
                            $validator->errors()->add("attendee-".$n."-field-email", $labels['EMAIL_ALREADY_EXISTS']);
                        });
                    }
                }
            }
        }

        $validate_attendee_response = AttendeeRepository::validateAttendeeRegistration($attendee_setting, $labels, request()->organizer_id, request()->event_id, $input['email'], "attendee-".$n."-field-email");

        if(!$validate_attendee_response['success']) {
            $validator->after(function ($validator) use ($validate_attendee_response) {
                $validator->errors()->add($validate_attendee_response['validate_index'], $validate_attendee_response['label']);
            });
        }

        //Registration Date
        if ($eventsite_setting['registration_end_date'] != '0000-00-00 00:00:00') {
            $currentDate = strtotime(\Carbon\Carbon::now());
            $startDate = strtotime(\Carbon\Carbon::parse($eventsite_setting['registration_end_date'])->toDateString().' '.$eventsite_setting['registration_end_time']);
            if ($currentDate > $startDate) {
                $validator->after(function ($validator) use ($labels) {
                    $validator->errors()->add('registration_end_date', $labels['REGISTER_DATE_END']);
                });
            }
        }

        //Remaining tickets
        $totalAttendees = \App\Models\EventAttendee::where('event_id', request()->event_id)->count();
    
        if (request()->event['ticket_left'] > 0 && request()->event['ticket_left'] != '') {
            if (request()->event['ticket_left'] <= $totalAttendees && $waiting_list_setting['status'] == '0') {
                $validator->after(function ($validator) use ($labels) {
                    $validator->errors()->add('ticket_left', $labels['REGISTER_TICKET_END']);
                });
            }
        }
    }
    
    /**
     * validateAttendeeAddons
     *
     * @param  mixed $validator
     * @param  mixed $labels
     * @param  mixed $input
     * @param  mixed $n
     * @return void
     */
    public function validateAttendeeAddons($validator, $items, $labels, $input, $n) {
        $selectedItemIds = Arr::pluck($input, 'id');

        //Existence
        foreach($selectedItemIds as $selectedItem) {
            if(!$this->validateItem($selectedItem)) {
                $validator->after(function ($validator) use ($labels, $n, $selectedItem) {
                    $validator->errors()->add("attendee-".$n."-group-item-".$selectedItem, "item not exist!");
                });
            }
        }

        foreach ($items['registrationItems'] as $key => $item) {
            if ($item['type'] == 'group') {
                $groupItemIds = Arr::pluck($item['group_data'], 'id');

                //Required group validate
                $count = count(array_intersect($groupItemIds, $selectedItemIds));
                if($count == 0 && $item['group_required'] == "yes") {
                    $validator->after(function ($validator) use ($labels, $n, $item) {
                        $validator->errors()->add("attendee-".$n."-group-".$item['id'], sprintf("Please select all required items for '%s'", $item['detail']['group_name']));
                    });
                }
                //End

                //Group items
                foreach($item['group_data'] as $groupItem) {
                    //Required group item validate 
                    if($groupItem['is_required'] == 1 && !in_array($groupItem['id'], $selectedItemIds)) {
                        $validator->after(function ($validator) use ($labels, $n, $groupItem) {
                            $validator->errors()->add("attendee-".$n."-group-item-".$groupItem['id'], sprintf("'%s' item is required!", $groupItem['detail']['item_name']));
                        });
                    }
                    //End

                    //Stock validate
                    $selectedItem = collect($input)->where('id', $groupItem['id'])->first();
                    if($selectedItem && $groupItem['total_tickets'] != 0 && $selectedItem['qty'] > $groupItem['remaining_tickets']) {
                        $validator->after(function ($validator) use ($labels, $n, $groupItem) {
                            $validator->errors()->add("attendee-".$n."-group-item-".$groupItem['id'], sprintf("'%s' item has no remaining tickets!", $groupItem['detail']['item_name']));
                        });
                    }
                    //End
                }
                
            } else {
                //Required item validate 
                if($item['is_required'] == 1 && !in_array($item['id'], $selectedItemIds)) {
                    $validator->after(function ($validator) use ($labels, $n, $item) {
                        $validator->errors()->add("attendee-".$n."-item-".$item['id'], sprintf("'%s' item is required!", $item['detail']['item_name']));
                    });
                }
                //End

                //Stock validate
                $selectedItem = collect($input)->where('id', $item['id'])->first();
                if($selectedItem && $item['total_tickets'] != 0  && $selectedItem['qty'] > $item['remaining_tickets']) {
                    $validator->after(function ($validator) use ($labels, $n, $item) {
                        $validator->errors()->add("attendee-".$n."-item-".$item['id'], sprintf("'%s' item has no remaining tickets!", $item['detail']['item_name']));
                    });
                }
                //End
            }
        }
    }
    
    /**
     * validateHotel
     *
     * @param  mixed $validator
     * @param  mixed $labels
     * @param  mixed $input
     * @return void
     */
    public function validateHotel($validator, $labels, $input) {
        if(isset($input['hotel']['selected_hotel_rooms'])) {
            //Validate selected hotels
            foreach($input['hotel']['selected_hotel_rooms'] as $hotel) {
                if($hotel['checkin'] && !validateDate($hotel['checkin'], 'd-m-Y')) {
                    $validator->after(function ($validator) use ($label, $hotel) {
                        $validator->errors()->add("hotels-checkin-".$hotel['id'], "Invalid date format.");
                    });  
                }
                if($hotel['checkout'] && !validateDate($hotel['checkout'], 'd-m-Y')) {
                    $validator->after(function ($validator) use ($label, $hotel) {
                        $validator->errors()->add("hotels-checkout-".$hotel['id'], "Invalid date format.");
                    });  
                }
                if(validateDate($hotel['checkin'], 'd-m-Y') && validateDate($hotel['checkout'], 'd-m-Y')) {
                    $hotelData = $this->hotelRepository->searchHotels(["event_id" => request()->event_id, "language_id" => request()->language_id, "checkin" => $hotel['checkin'], "checkout" => $hotel['checkout'], "room" => $hotel['rooms'], "hotel_ids" => [$hotel['id']]]);
                    if(!$hotelData) {
                        $label = $labels['EVENTSITE_HOTEL_CHECK_ON_ANOTHER_DAY'];
                        if(is_null($label) || empty($label)) {
                            $label = 'Someone booked these rooms, Please check on another days';
                        }
                        $validator->after(function ($validator) use ($label, $hotel) {
                            $validator->errors()->add("hotels-".$hotel['id'], $label);
                        });
                    }
                }
            }
        }
    }
    
    /**
     * validatePaymentInformation
     *
     * @param  mixed $validator
     * @param  mixed $labels
     * @param  mixed $input
     * @param  mixed $sections
     * @return void
     */
    public function validatePaymentInformation($validator, $labels, $input, $sections) {
        $eventsite_setting = request()->event['eventsite_setting'];
        foreach ((array) $sections as $section) {
            if(in_array($section['field_alias'], ["company_detail", "po_number"])) {
                foreach ($section['fields'] as $field) {
                    if (!in_array($field['field_alias'], ["EMPLOYMENT_DATE"])) {
                        if(in_array($field['field_alias'], ["contact_person_email"]) && $input[$field['field_alias']]) {
                            if(!validateEmail($input[$field['field_alias']])) {
                                $validator->after(function ($validator) use ($labels, $field, $input) {
                                    $validator->errors()->add("payment-field-".$field['field_alias'], "Invalid email!");
                                });
                            }
                        } else if ($field['mandatory'] == 1 && !in_array($field['field_alias'], ["custom_field_id", "credit_card_payment", "company_invoice_payment", "company_public_payment"])) {
                            if($field['field_alias'] == "ean" && $eventsite_setting['payment_type'] == 1) {
                                if(isset($input['company_type']) && $input['company_type'] == "public" && !EventSiteSettingRepository::validateEan($input[$field['field_alias']])['status']) {
                                    $validator->after(function ($validator) use ($labels, $field, $input) {
                                        $validator->errors()->add("payment-field-".$field['field_alias'], "Ean no is not valid.");
                                    });
                                }
                            } else if($field['field_alias'] == "company_registration_number" && $eventsite_setting['payment_type'] == 1) {
                                if(!EventSiteSettingRepository::validateCvr($input[$field['field_alias']])) {
                                    $validator->after(function ($validator) use ($labels, $field, $input) {
                                        $validator->errors()->add("payment-field-".$field['field_alias'], "CVR number is not valid.");
                                    });
                                }
                            } else {
                                if (!in_array($field['field_alias'], ["ean", "company_registration_number"])) {
                                    $validator->after(function ($validator) use ($labels, $field, $input) {
                                        if(!isset($input[$field['field_alias']]) || !$input[$field['field_alias']]) {
                                            $validator->errors()->add("payment-field-".$field['field_alias'], $labels['REGISTRATION_FORM_FIELD_REQUIRED']);
                                        }
                                    });
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    /**
     * validateVoucher
     *
     * @param  mixed $validator
     * @param  mixed $labels
     * @param  mixed $input
     * @return void
     */
    public function validateVoucher($validator, $labels, $input) {
        if(isset($input['voucher_code']) && $input['voucher_code']) {
            $voucher = \App\Models\BillingVoucher::where(function ($q) use ($input) {
                $q->where('event_id', request()->event_id)
                    ->where('code', $input['voucher_code'])
                    ->where('status', '1');
            })
            ->where(function ($query) {
                $query->where('expiry_date', '0000-00-00')->orWhere(function ($query) {
                    $query->whereDate('expiry_date', '>=', \Carbon\Carbon::now()->format('Y-m-d'));
                });
            })->first();

            if(!$voucher) {
                $validator->after(function ($validator) use ($labels) {
                    $validator->errors()->add("voucher", "Invalid voucher code");
                });
            }
        }
    }
    
    /**
     * validateItem
     *
     * @param  mixed $item_id
     * @return void
     */
    public function validateItem($item_id) {
        return \App\Models\BillingItem::where('id',$item_id)->where('event_id', request()->event_id)->where('organizer_id', request()->organizer_id)->count();
    }
}
