<?php

namespace App\Http\Controllers\Auth\Attendee\Requests;

use App\Http\Requests\Api\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ForgotPasswordRequest extends FormRequest
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
            'email' => 'bail|required|email'
        ];
    }

    public function messages()
    {
        $event = request()->event;
        $label = $event['labels'];
        return [
            'email.email' => $label['GENERAL_ENTER_EMAIL_MSG'],
            'email.required' => $label['GENERAL_VALID_ENTER_EMAIL_MSG']
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