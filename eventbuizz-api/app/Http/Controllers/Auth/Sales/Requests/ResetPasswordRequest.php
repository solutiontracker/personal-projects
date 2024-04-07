<?php

namespace App\Http\Controllers\Auth\Sales\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordRequest extends FormRequest
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
            'reset_code' => 'bail|required',
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6|max:190|confirmed',
        ];
    }


    /**
     * custom validation messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'reset_code.required' => 'Insufficient reset information provided',
            'email.required' => 'Insufficient reset information provided',
        ];
    }


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        return [];
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

