<?php

namespace App\Http\Controllers\Wizard\Requests\Organizer;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class OrganizerRequest extends FormRequest
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
        $rules = [];
        if (\Route::is('wizard-organizer-profile')  && request()->isMethod('PUT')) {
            $rules = [
                'first_name' => 'bail|required',
                'phone' => 'bail|required',
            ];

            $rules['email'] = [
                'bail',
                'required',
                Rule::unique('conf_organizer')->where(function ($query) {
                    return $query->where('email', $this->request->get('email'))
                        ->where('id', '!=', request()->id)
                        ->whereNull('deleted_at');
                }),
            ];
        } else if (\Route::is('wizard-organizer-change-password') && request()->isMethod('PUT')) {
            $rules = [
                'current_password' => 'bail|required',
                'password' => 'bail|required|confirmed|min:6',
            ];
        }
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
        // checks user current password
        if (\Route::is('wizard-organizer-change-password') && request()->isMethod('PUT')) {
            $validator->after(function ($validator) {
                if (!\Hash::check($this->current_password, $this->user()->password) && request()->current_password) {
                    $validator->errors()->add('current_password', __('messages.current_password_not_match'));
                }
            });
        }
        return;
    }
}
