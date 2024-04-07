<?php

namespace App\Http\Controllers\Auth\Lead\Requests;
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
        $authMode = request()->login_with_auth_code;

        return [
            'email' => 'bail|required|email',
            'password' => $authMode ? '' : 'required',
            'event_id' => 'integer|required'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => "Email is required",
            'email.email' => "Email is not valid",
            'password.required' => "Password is required"
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
            "status" => 0,
            "respons_type" => INVALID_CREDENTIALS,
            "message" => is_countable($validator->errors()->all()) ? $validator->errors()->all()[0] : $validator->errors()->all(),
        ), 422));
    }
}
