<?php

namespace App\Http\Controllers\Wizard\Requests\EventInfo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class LinkRequest extends FormRequest
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
        if(isset($params['type']) && $params['type'] == "link") {
            return [
                'name' => 'required',
                'url' => 'required',
                'menu_id' => 'required',
                'page_type' => 'required',
                'page_type.required' => 'Page type is required.',
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
