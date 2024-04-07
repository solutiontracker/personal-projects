<?php

namespace App\Http\Controllers\Wizard\Requests\EventSetting;

use Illuminate\Foundation\Http\FormRequest;

class BrandingRequest extends FormRequest
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
            'header_logo' => (request()->header_logo && is_base64(request()->header_logo) ? 'bail|required|base64image' : ''),
            'app_icon' => (request()->app_icon && is_base64(request()->app_icon) ? 'bail|required|base64image' : ''),
            'social_media_logo' => (request()->social_media_logo && is_base64(request()->social_media_logo) ? 'bail|required|base64image' : ''),
            'fav_icon' => (request()->fav_icon && is_base64(request()->fav_icon) ? 'bail|required|base64image' : ''),
            'eventsite_banners.*' => (request()->hasFile('eventsite_banners') ? 'bail|required|image|mimes:jpeg,png,jpg,gif,svg' : ''),
        ];

        return $rules;
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $eventsite_banners_errors = [];
        foreach ($validator->errors()->all() as $error) {
            if (str_contains($error, 'eventsite_banners')) {
                array_push($eventsite_banners_errors, $error);
            }
        }
        if (!empty($eventsite_banners_errors)) {
            $validator->after(function ($validator) {
                $validator->errors()->add('eventsite_banners', __('messages.banners'));
            });
        }
    }
}
