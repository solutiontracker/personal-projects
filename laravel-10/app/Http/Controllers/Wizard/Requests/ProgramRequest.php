<?php

namespace App\Http\Controllers\Wizard\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProgramRequest extends FormRequest
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
        return [
            'topic' => 'bail|required',
            'date' => 'bail|required|date',
            'start_time' => 'bail|required|date_format:H:i:s',
            'end_time' => 'bail|required|date_format:H:i:s|after:start_time'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'start_time' => (request()->start_time ? \Carbon\Carbon::parse(request()->start_time)->toTimeString() : ''),
            'end_time' => (request()->end_time ? \Carbon\Carbon::parse(request()->end_time)->toTimeString() : ''),
            'date' => (request()->date ? \Carbon\Carbon::parse(request()->date)->toDateString() : ''),
        ]);
    }
}