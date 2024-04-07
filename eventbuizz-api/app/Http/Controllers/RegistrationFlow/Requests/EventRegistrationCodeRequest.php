<?php

namespace App\Http\Controllers\RegistrationFlow\Requests;

use App\Eventbuizz\Repositories\EventSiteRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Phone;

class EventRegistrationCodeRequest extends FormRequest
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
        return [
            'code' => 'bail|required',
            'email' => 'bail|required|email'
        ];
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

            $input = request()->all();

            $organizer_id = $input['organizer_id'];

            $event_id = $input['event_id'];

            $labels = request()->event['labels'];

            $eventsite_setting  = request()->event['eventsite_setting'];

            $attendee_setting = request()->event['attendee_setting'];

            if($eventsite_setting['registration_code'] == $input['code']) {

                $domains = array();

                if (trim($attendee_setting['domain_names'])) {
                    $domain_names   = explode(',',$attendee_setting['domain_names']);
                    foreach ($domain_names as $domain) {
                        $domains[trim(strtolower($domain))] = trim(strtolower($domain));
                    }
                }

                if($attendee_setting["validate_attendee_invite"] == "1" || count($domains) > 0) {
                    if(trim($input['email']) == '') {
                        $validator->after(function ($validator) use ($labels) {
                            $validator->errors()->add('email', $labels['REGISTRATION_ERROR_EMAIL']);
                        });
                    }
                }


                if (count($domains) > 0) {
                    $domain_data = explode("@", $input['email']);
                    if (!in_array(strtolower(trim($domain_data[1])), $domains)) {
                        $validator->after(function ($validator) use ($labels) {
                            $validator->errors()->add('email', $labels['REGISTER_VALID_DOMAIN']);
                        });
                    }
                }

                if($attendee_setting["validate_attendee_invite"] == "1") {
                    $invite = \App\Models\AttendeeInvite::where('organizer_id', $organizer_id)->where('event_id', $event_id)->where('email', $input['email'])->first();
                    if(!$invite) {
                        $attendee = \App\Models\Attendee::where('email', $input['email'])->where('organizer_id', $organizer_id)->first();
                        if($attendee) {
                            $event_attendee = \App\Models\EventAttendee::where('attendee_id', $attendee->id)->where('event_id', $event_id)->first();
                            if(!$event_attendee) {
                                $validator->after(function ($validator) use ($labels) {
                                    $validator->errors()->add('email', $labels['REGISTRATION_INVITE_VALIDATION_ERROR']);
                                });
                            }
                        } else {
                            $validator->after(function ($validator) use ($labels) {
                                $validator->errors()->add('email', $labels['REGISTRATION_INVITE_VALIDATION_ERROR']);
                            });
                        }
                    }
                }
            }
            else {
                $validator->after(function ($validator) use ($labels) {
                    $validator->errors()->add('code', "Enter a valid registration code.");
                });
            }
        }
    }
}
