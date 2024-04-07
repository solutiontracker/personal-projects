<?php

namespace App\Http\Controllers\Wizard\Requests\eventsite\billing;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class VoucherRequest extends FormRequest
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
        $rules = array();
        
        if (request()->isMethod('PUT') && (\Route::is('wizard-eventsite-billing-voucher-edit') || \Route::is('wizard-eventsite-billing-voucher-create')) && request()->type == "order") {
            $rules = [
                'type' => 'bail|required',
                'voucher_name' => 'bail|required',
                'discount_type' => 'bail|required',
                'price' => 'bail|required|numeric',
            ];

            return $rules;
        } else if (request()->isMethod('PUT') && (\Route::is('wizard-eventsite-billing-voucher-edit') || \Route::is('wizard-eventsite-billing-voucher-create')) && request()->type == "billing_items") {
            $rules = [
                'type' => 'bail|required',
                'voucher_name' => 'bail|required',
            ];
        }

        if (\Route::is('wizard-eventsite-billing-voucher-create') && request()->isMethod('PUT')) {
            $rules['code'] = [
                'bail',
                'required',
                Rule::unique('conf_billing_vouchers')->where(function ($query) {
                    return $query->where('code', $this->request->get('code'))
                        ->where("event_id", $this->request->get('event_id'))
                        ->whereNull('deleted_at');
                }),
            ];
        } else if (\Route::is('wizard-eventsite-billing-voucher-edit') && request()->isMethod('PUT')) {
            $rules['code'] = [
                'bail',
                'required',
                Rule::unique('conf_billing_vouchers')->where(function ($query) {
                    return $query->where('code', $this->request->get('code'))
                        ->where('id', '!=', request()->id)
                        ->where("event_id", $this->request->get('event_id'))
                        ->whereNull('deleted_at');
                }),
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
        if (request()->isMethod('PUT') && (\Route::is('wizard-eventsite-billing-voucher-edit') || \Route::is('wizard-eventsite-billing-voucher-create')) && request()->type == "billing_items") {
            $validate = $this->checkPrices();
            if ($validate) {
                $validator->after(function ($validator) use ($validate) {
                    $validator->errors()->add('voucher_items', $validate);
                });
            }
        }
    }
    public function checkPrices()
    {
        if (request()->items) {
            $items =  request()->items;
            foreach ($items as $key => $row) {
                if (!$row["discount_price"] && $row["checked"]) {
                    $errors[] = $row["detail"]["item_name"] . ' price is required';
                }
            }
            if (count($errors ?? []) > 0) {
                $error_msg = implode("<br>", $errors ?? []);
                return $error_msg;
            } else {
                return false;
            }
        }
        return false;
    }
}
