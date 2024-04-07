<?php

namespace App\Http\Controllers\Wizard\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

use Illuminate\Support\Str;

class EventRequest extends FormRequest
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
        $rules = [
            'support_email' => 'bail|required|email',
            //'url' => 'bail|required',
            'organizer_name' => 'bail|required',
            'timezone_id' => 'bail|required',
            //'dateformat' => 'bail|required',
            'start_date' => 'bail|required|date' . (\Route::is('wizard-event-store') ? '|after_or_equal:today' : ''),
            'end_date' => 'bail|required|date|after_or_equal:start_date',
            'cancellation_date' => ($this->request->get('cancellation_date') ? 'bail|required|date|not_in:0000-00-00 00:00:00' . (\Route::is('wizard-event-store') ? '|after_or_equal:today' : '') : ''),
            'registration_end_date' => ($this->request->get('registration_end_date') ? 'bail|required|date|not_in:0000-00-00 00:00:00' . (\Route::is('wizard-event-store') ? '|after_or_equal:today' : '') : ''),
            'country_id' => 'bail|required',
            //'assign_package_id' => 'required|exists:conf_assign_packages,id,organizer_id,'. $this->request->get('organizer_id'),
            'location_name' => 'bail|required',
            'location_address' => 'bail|required',
            'start_time' => 'bail|required|date_format:H:i:s',
            'end_time' => 'bail|required|date_format:H:i:s' . ($this->request->get('start_date') == $this->request->get('end_date') ? '|after:start_time' : ''),
            'sms_organizer_name' => 'bail|required',
        ];

        if (\Route::is('wizard-event-store')) {
            $rules['name'] = [
                'bail',
                'required',
            ];
        } else {
            $rules['name'] = [
                'bail',
                'required'
            ];
        }
        return $rules;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'url' => Str::slug(request()->name, '-'),
            'start_date' => (request()->start_date ? \Carbon\Carbon::parse(request()->start_date)->toDateString() : ''),
            'end_date' => (request()->end_date ? \Carbon\Carbon::parse(request()->end_date)->toDateString() : ''),
            'start_time' => (request()->start_time ? \Carbon\Carbon::parse(request()->start_time)->toTimeString() : ''),
            'end_time' => (request()->end_time ? \Carbon\Carbon::parse(request()->end_time)->toTimeString() : ''),
            'cancellation_date' => (request()->cancellation_date && request()->cancellation_date != "0000-00-00 00:00:00" ? \Carbon\Carbon::parse(request()->cancellation_date)->toDateString() : ''),
            'registration_end_date' => (request()->registration_end_date && request()->registration_end_date != "0000-00-00 00:00:00" ? \Carbon\Carbon::parse(request()->registration_end_date)->toDateString() : ''),
        ]);
    }
}
