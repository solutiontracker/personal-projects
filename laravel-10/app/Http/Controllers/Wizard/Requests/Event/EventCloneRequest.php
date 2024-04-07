<?php

namespace App\Http\Controllers\Wizard\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

use Illuminate\Support\Str;

class EventCloneRequest extends FormRequest
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
            'start_date' => 'bail|required|date' . (\Route::is('wizard-event-store') ? '|after_or_equal:today' : ''),
            'end_date' => 'bail|required|date|after_or_equal:start_date',
            'location_name' => 'bail|required',
            'start_time' => 'bail|required|date_format:H:i:s',
            'end_time' => 'bail|required|date_format:H:i:s' . ($this->request->get('start_date') == $this->request->get('end_date') ? '|after:start_time' : ''),
        ];

        $rules['name'] = [
            'bail',
            'required'
        ];
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
        ]);
    }
}
