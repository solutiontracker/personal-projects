<?php

namespace App\Http\Controllers\RegistrationFlow\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;

class SearchHotelRequest extends FormRequest
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
            'checkin' => 'bail|required|date_format:Y-m-d',
            'checkout' => $this->request->get('checkin') ? 'bail|required|date_format:Y-m-d' : ''
        ];
    }

    public function messages()
    {
        $message = array();
        $labels = request()->event['labels'];
        $message['checkin.required'] =  $labels['REGISTRATION_FORM_CHECKIN_FIELD_REQUIRED'];
        $message['checkout.required'] =  $labels['REGISTRATION_FORM_CHECKOUT_FIELD_REQUIRED'];
        return $message;
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
