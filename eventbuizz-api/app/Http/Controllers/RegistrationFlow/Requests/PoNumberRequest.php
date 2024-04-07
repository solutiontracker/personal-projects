<?php

namespace App\Http\Controllers\RegistrationFlow\Requests;

use App\Eventbuizz\Repositories\EventSiteSettingRepository;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\Phone;

use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;

class PoNumberRequest extends FormRequest
{
    protected $rules = [];

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
        $input = $this->request->all();

        $method = $this->method();

        if (null !== $this->get('_method', null)) {
            $method = $this->get('_method');
        }

        $this->offsetUnset('_method');

        switch ($method) {
            case 'DELETE':
            case 'GET':
                break;
            case 'POST':
                $this->rules['poNumber'] = 'bail|required|size:10|starts_with:403,404,405,409';
                break;
            case 'PUT':
            case 'PATCH':
                break;
            default:
                break;
        }

        return $this->rules;

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
    
    /**
     * messages
     *
     * @return void
     */
    public function messages()
    {
        $message = array();

        $labels = request()->event['labels'];

        $message[$field['field_alias'] . '.required'] = $labels['REGISTRATION_FORM_FIELD_REQUIRED'];
        
        $message[$field['field_alias'] . '.size'] = $labels['REGISTRATION_FORM_PO_NUMBER_TEN_DIGIT_AND_START_WITH'];

        $message[$field['field_alias'] . '.starts_with'] = $labels['REGISTRATION_FORM_PO_NUMBER_TEN_DIGIT_AND_START_WITH'];
        
        return $message;
    }
}
