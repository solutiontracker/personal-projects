<?php

namespace App\Http\Controllers\Auth\ReportingAgent\Requests;
use App\Http\Requests\Api\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class VerificationRequest extends FormRequest
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
            "email"=> "bail|required|email",
            "token"=> 'required'
        ];
    }

    public function messages()
    {
        $event = request()->event;

        $label = $event['labels'];

        return [
            'email.required' => "Email is required",
            'email.email' => "Email is not valid",
            'token.required' => "Reset token is required"
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
            'success' => 0,
            "respons_type" => INVALID_CREDENTIALS,
            "message" => is_countable($validator->errors()->all()) ? $validator->errors()->all()[0] : $validator->errors()->all(),
        ), 422));
    }
}
