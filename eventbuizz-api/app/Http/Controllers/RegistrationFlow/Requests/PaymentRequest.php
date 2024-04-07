<?php

namespace App\Http\Controllers\RegistrationFlow\Requests;

use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Phone;

class PaymentRequest extends FormRequest
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
                
                foreach ((array) $this->request->get('sections') as $section) {
                    if(in_array($section['field_alias'], ["company_detail", "po_number"])) {
                        foreach ($section['fields'] as $field) {
                            if ($field['mandatory'] == 1 && in_array($field['field_alias'], ["contact_person_email", "contact_person_confirm_email"])) {
                                $this->rules[$field['field_alias']] = 'bail|email';
                                $this->rules['contact_person_confirm_email'] = 'bail|required_with:email|same:contact_person_email';
                            } else if (in_array($field['field_alias'], ["poNumber"])) {
                                if((!$input['validate_po_number'] || ($input['validate_po_number'] && !$input['poNumber'])) && ((isset($input['validate_burger_id']) && $input['validate_burger_id'] == 1) || $input['bruger_id'])) {
                                    $this->rules[$field['field_alias']] = 'bail|required|size:10|starts_with:403,404,405,409';
                                } else if($field['mandatory'] == 1) {
                                    $this->rules[$field['field_alias']] = 'bail|required';
                                }
                            } else if ((isset($input[$field['field_alias']]) && $input[$field['field_alias']]) && ($field['mandatory'] == 1 || $input[$field['field_alias']]) && in_array($field['field_alias'], ["contact_person_mobile_number"])) {
                                $this->rules[$field['field_alias']] = [
                                    'bail',
                                    'required',
                                    new Phone()
                                ];
                            } else if ($field['mandatory'] == 1 && (!in_array($field['field_alias'], ["custom_field_id", "company_public_payment", "credit_card_payment", "company_invoice_payment", "member_number", "company_street", "company_house_number", "company_post_code", "company_city", "company_country", "ean"]) 
                            || ($field['field_alias'] == "credit_card_payment" && $input['company_type'] != "private" && !in_array(request()->provider, ['sale']))
                            || ($field['field_alias'] == "company_invoice_payment" && $input['company_type'] != "invoice")
                            || ($field['field_alias'] == "company_public_payment" && $input['company_type'] != "public"))) {
                                $this->rules[$field['field_alias']] = 'bail|required';
                            }
                        }
                    }
                }

                if (isset($input['company_type']) && $input['company_type'] == "public") {
                    $this->rules['company_street'] = 'bail|required';
                    $this->rules['company_house_number'] = 'bail|required';
                    $this->rules['company_post_code'] = 'bail|required';
                    $this->rules['company_city'] = 'bail|required';
                    $this->rules['company_country'] = 'bail|required';
                    $this->rules['ean'] = 'bail|required';
                    $this->rules['company_registration_number'] = 'bail|required';
                    $this->rules['bruger_id'] = isset($input['validate_burger_id']) && $input['validate_burger_id'] == 1 ? 'bail|required' : '';
                }

                if($input['member'] == 1) {
                    $this->rules['member_number'] = 'bail|required';
                }
                
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
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if(request()->isMethod('POST')) {
            $labels = request()->event['labels'];

            //Payment information validation
            $this->validatePaymentInformation($validator, $labels, $this->request->get('sections'));
        }
    }

    /**
     * validatePaymentInformation
     *
     * @param  mixed $validator
     * @param  mixed $labels
     * @param  mixed $sections
     * @return void
     */
    public function validatePaymentInformation($validator, $labels, $sections) {
        $input = $this->request->all();
        foreach ((array) $sections as $section) {
            if(in_array($section['field_alias'], ["company_detail", "po_number"])) {
                foreach ($section['fields'] as $field) {
                    if (!in_array($field['field_alias'], ["EMPLOYMENT_DATE"])) {
                        if ($input[$field['field_alias']] && !in_array($field['field_alias'], ["custom_field_id", "credit_card_payment", "company_invoice_payment", "company_public_payment"])) {
                            if($field['field_alias'] == "ean") {
                                if(isset($input['company_type']) && $input['company_type'] == "public" && !EventSiteSettingRepository::validateEan($this->request->get('ean'))['status'] && (!$input['validate_ean'] || ($input['validate_ean'] && !$this->request->get('ean')))) {
                                    $validator->after(function ($validator) use ($labels) {
                                        $validator->errors()->add("ean", $labels['REGISTRATION_FORM_EAN_IS_NOT_VALID']);
                                    });
                                }
                            } else if($field['field_alias'] == "company_registration_number") {
                                if(!EventSiteSettingRepository::validateCvr($this->request->get('company_registration_number')) && isset($input['company_type']) && $input['company_type'] == "public" && (!$input['validate_cvr'] || ($input['validate_cvr'] && !$this->request->get('company_registration_number')))) {
                                    $validator->after(function ($validator) use ($labels) {
                                        $validator->errors()->add("company_registration_number", $labels['REGISTRATION_FORM_INVALID_CVR_NUMBER']);
                                    });
                                }
                            }
                        }
                    }
                }
            }
        }
        
        //Ean specific validation
        if (isset($input['company_type']) && $input['company_type'] == "public") {
            if(!isset($input['company_street']) || !isset($input['company_house_number']) || !isset($input['company_post_code']) || !isset($input['company_city']) || !isset($input['company_country']) || !isset($input['ean'])) {
                $validator->after(function ($validator) use ($labels) {
                    $validator->errors()->add("ean", $labels['REGISTRATION_FORM_EAN_FIELD_REQUIRED']);
                });
            }
        }
    }

    public function messages()
    {
        $message = array();

        $labels = request()->event['labels'];

        foreach ((array) $this->request->get('sections') as $section) {
            foreach ($section['fields'] as $field) {
                if (!in_array($field['field_alias'], ["custom_field_id", "credit_card_payment", "company_invoice_payment", "company_public_payment"])) {
                    $message[$field['field_alias'] . '.required'] = $labels['REGISTRATION_FORM_FIELD_REQUIRED'];
                }
            }
        }
        
        $message[$field['field_alias'] . '.size'] = $labels['REGISTRATION_FORM_PO_NUMBER_TEN_DIGIT_AND_START_WITH'];

        $message[$field['field_alias'] . '.starts_with'] = $labels['REGISTRATION_FORM_PO_NUMBER_TEN_DIGIT_AND_START_WITH'];
        
        $message['company_street.required'] = __('messages.field_required');

        $message['company_house_number.required'] = __('messages.field_required');

        $message['company_post_code.required'] = __('messages.field_required');

        $message['company_city.required'] = __('messages.field_required');

        $message['company_country.required'] = __('messages.field_required');

        $message['ean.required'] = __('messages.field_required');
        
        return $message;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if(request()->route('order_id') && request()->route('attendee_id')) {
            $order = \App\Models\BillingOrder::where('order_id', request()->route('order_id'))->first();
            $registration_form = (object)EventSiteSettingRepository::getRegistrationForm(["event_id" => request()->event_id, 'type_id' => $order->attendee_type]);
            $registration_form_id = $registration_form ? $registration_form->id : 0;
        } else {
            $registration_form_id = 0;
        }

        $sections = EventSiteSettingRepository::getAllSections(["event_id" => request()->event_id, "language_id" => request()->language_id, "status" => 1, 'registration_form_id' => $registration_form_id]);

        if (strpos($this->contact_person_mobile_number, '+') === false && $this->contact_person_mobile_number) {
            $phone = $this->calling_code_contact_person_mobile_number . '-' . $this->contact_person_mobile_number;
        } else if ($this->contact_person_mobile_number) {
            $phone = $this->contact_person_mobile_number;
        } else {
            $phone = "";
        }

        $this->merge([
            'sections' => $sections,
            'contact_person_mobile_number' => $phone,
        ]);
    }
}
