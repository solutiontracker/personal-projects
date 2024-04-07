<?php

namespace App\Http\Controllers\Auth\Attendee\Requests;
use App\Http\Requests\Api\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class LoginRequest extends FormRequest
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
        $event = request()->event;

        $attendee_setting = getEventAttendeeSetting($event['id']);

        return [
            'email' => 'bail|required|email',
            'password' => (!$attendee_setting->authentication && !$attendee_setting->registration_password && !$attendee_setting->hide_password ? 'bail|required' : ''),
            'remember_me' => (!$attendee_setting->authentication && !$attendee_setting->registration_password && !$attendee_setting->hide_password ? 'bail|boolean' : '')
        ];
    }

    public function messages()
    {
        $event = request()->event;

        $label = $event['labels'];

        return [
            'email.required' => $label['GENERAL_ENTER_EMAIL_MSG'],
            'email.email' => $label['GENERAL_VALID_ENTER_EMAIL_MSG'],
            'password.required' => $label['GENERAL_PASSWORD_INCORRECT']
        ];
    }

    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        return [
            'email' => 'trim|lowercase',
        ];
    }

    /**
     *  update response
     *
     * @param object
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(array(
            "success" => false,
            "message" => set_error_delimeter($validator->errors()->all()),
        ), 422));
    }
}
