<?php

namespace App\Http\Controllers\Wizard\Requests\Map;

use Illuminate\Foundation\Http\FormRequest;

class MapRequest extends FormRequest
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
            'url' => 'bail|required_if:google_map,1',
            'image' => (request()->hasFile('image') ? 'bail|required_if:google_map,0|image|mimes:jpeg,png,jpg,gif' : 'bail|required_if:google_map,0')
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'url.required_if' => trans('messages.google_map_if_required_error')
        ];
    }

}
