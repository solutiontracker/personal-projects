<?php

namespace App\Http\Controllers\Wizard\Requests;

use App\Models\Attendee;
use App\Models\AttendeeInvite;
use Illuminate\Foundation\Http\FormRequest;

class AttendeeRequest extends FormRequest
{
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
        if (\Route::is('wizard-attendee-save-invitation')) {
            return [
                'first_name' => 'bail|required',
                'email' => (\Route::is('wizard-attendee-save-invitation') && request()->isMethod('POST') ? 'bail|required|email|unique:conf_attendee_invites,email,null,null,deleted_at,NULL,organizer_id,' . organizer_id() . ',event_id,' . request()->event_id : 'bail|required|email|unique:conf_attendee_invites,email,' . request()->id . ',id,deleted_at,NULL,organizer_id,' . organizer_id() . ',event_id,' . request()->event_id),
                'ss_number' => 'nullable|digits:10',
            ];
        } else if (\Route::is('wizard-attendee-attendee-type')) {
            return [
                'name' => 'bail|required|unique:conf_event_attendee_type,attendee_type,null,null,deleted_at,NULL,event_id,' . request()->event_id,
            ];
        } else {

            $rules = ['first_name' => 'bail|required', 'ss_number' => 'nullable|digits:10', 'program_id' => (\Route::is('wizard-attendee-store') && request()->speaker == 1 ? 'bail|required|integer' : '')];
            if(request()->speaker == 1 && \Route::is('wizard-attendee-store')) {
                $rules['email'] = 'bail|required|email';
            } else {
                $rules['email'] = (\Route::is('wizard-attendee-store') ? 'bail|required|email' : 'bail|required|email|unique:conf_attendees,email,' . request()->id . ',id,deleted_at,NULL,organizer_id,'.organizer_id());
            }

            return $rules;
        }
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if ((\Route::is('wizard-attendee-save-invitation') || \Route::is('wizard-attendee-store')) && !request()->speaker) {
            $attendee_id = \App\Models\Attendee::where('email', request()->email)->where('organizer_id', organizer_id())->value('id');
            if ($attendee_id) {
                $count = \App\Models\EventAttendee::where('attendee_id', $attendee_id)->where('event_id', request()->event_id)->count();
                if ($count > 0) {
                    $validator->after(function ($validator) {
                        $validator->errors()->add('email', __('messages.invitation_event_already_assigned'));
                    });
                }
            }

            // check ss_number unique validation.
            // if(request()->ss_number) {
            //     $invite_query = AttendeeInvite::where('organizer_id', organizer_id())->where('event_id', request()->event_id)->where('ss_number', md5(request()->ss_number))->where('email', '!=', request()->email);
            //     if (request()->isMethod('PUT')) {
            //         $invite_query->where('id', '!=', request()->id);
            //     }

            //     $invite = $invite_query->first();
            //     $attendee = Attendee::where('organizer_id', organizer_id())->where('ss_number', md5(request()->ss_number))->where('email', '!=', request()->email)->first();
            //     if ($attendee || $invite) {
            //         $validator->after(function ($validator) {
            //             $validator->errors()->add('ss_number', __('messages.ss_number_in_use'));
            //         });
            //     }
            // }
        }

        // Validation for ss_number for attendee create and update.
        // if((\Route::is('wizard-attendee-store') || \Route::is('wizard-attendee-update')) && request()->ss_number){
        //     $query = Attendee::where('organizer_id', organizer_id())->where('ss_number', md5(request()->ss_number));

        //     if(\Route::is('wizard-attendee-update')){
        //         $query->where('id', '!=', request()->id);
        //     }

        //     $attendee = $query->first();

        //     if($attendee){
        //         $validator->after(function ($validator){
        //             $validator->errors()->add('ss_number', __('messages.ss_number_in_use'));
        //         });
        //     }
        // }
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'ss_number' => str_replace('-', '', request()->ss_number),
        ]);
    }

    public function messages()
    {
        return [
            'ss_number.digits' => __('messages.cpr_digit_validation_error'),
        ];
    }
}
