<?php

namespace App\Http\Controllers\Wizard\Requests\EventInfo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PageRequest extends FormRequest
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
        $params = request()->route()->parameters();
        if (isset($params['type']) && $params['type'] == "page") {
            return [
                'name' => 'required',
                'image' => (request()->hasFile('image') ? 'bail|required|image|mimes:jpeg,png,jpg,gif,svg' : ''),
                'pdf' => (request()->hasFile('pdf') ? 'bail|required|mimes:pdf' : ''),
                'menu_id' => 'required',
                'page_type' => 'required',
            ];
        } else {
            return [];
        }
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'menu_id' => (request()->menu_id ? request()->menu_id : 0)
        ]);
    }
}
