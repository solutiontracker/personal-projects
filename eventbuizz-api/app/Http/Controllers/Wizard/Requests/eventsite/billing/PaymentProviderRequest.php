<?php

namespace App\Http\Controllers\Wizard\Requests\eventsite\billing;

use Illuminate\Foundation\Http\FormRequest;

class PaymentProviderRequest extends FormRequest
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
        if (request()->isMethod('PUT') && \Route::is('wizard-eventsite-billing-payment-providers')) {
            return [
                //'mistertango_markets' => 'bail|required_if:billing_merchant_type,2',
                'billing_yourpay_language' => 'bail|required_if:billing_merchant_type,2',
                'swed_bank_password' => 'bail|required_if:billing_merchant_type,4',
                'qp_agreement_id' => 'bail|required_if:billing_merchant_type,5',
                'qp_secret_key' => 'bail|required_if:billing_merchant_type,5',
                'wc_customer_id' => 'bail|required_if:billing_merchant_type,6',
                'wc_secret' => 'bail|required_if:billing_merchant_type,6',
                'wc_shop_id' => 'bail|required_if:billing_merchant_type,6',
                'stripe_api_key' => 'bail|required_if:billing_merchant_type,7',
                'stripe_secret_key' => 'bail|required_if:billing_merchant_type,7',
            ];
        } else if (request()->isMethod('PUT') && \Route::is('wizard-eventsite-billing-fik-settings')) {
            return [
                'debitor_number' => 'bail|required|digits:8'
            ];
        } else {
            return [];
        }
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        if (request()->isMethod('PUT') && \Route::is('wizard-eventsite-billing-payment-providers')) {
            return [
                'mistertango_markets.required_if' => "Mistertango Markets is required",
                'billing_yourpay_language.required_if' => "Payment Language is required",
                'swed_bank_password.required_if' => "Swedbank processing password is required when payment method is swedbank",
                'qp_agreement_id.required_if' => "Agreement ID is required when payment method is QuickPay",
                'qp_secret_key.required_if' => "QuickPay Secret Key is required when payment method is QuickPay",
                'wc_customer_id.required_if' => "Wirecard customer ID is required when payment method is Wirecard",
                'wc_secret.required_if' => "Wirecard secret is required when payment method is Wirecard",
                'wc_shop_id.required_if' => "Wirecard shop ID is required when payment method is Wirecard",
                'stripe_api_key.required_if' => "Stripe public API key is required when payment method is Stripe",
                'stripe_secret_key.required_if' => "Stripe secret API key is required when payment method is Stripe",
            ];
        } else {
            return [];
        }
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if (request()->isMethod('PUT')) {
            if (request()->billing_item_type) {
                $current_item_type = \App\Models\EventsitePaymentSetting::where('event_id', request()->event_id)->where('registration_form_id', 0)->value("billing_item_type");
                $new_item_type = request()->billing_item_type;
                if ($current_item_type != $new_item_type) {
                    $type_array = array('0' => 'program', '1' => 'track', '2' => 'workshop', '3' => 'attendee_group');
                    $billing_items_count = \App\Models\BillingItem::where("event_id", request()->event_id)->where("link_to", $type_array[$current_item_type])->where("is_archive", 0)->count();
                    if ($billing_items_count > 0) {
                        $validator->after(function ($validator) {
                            $validator->errors()->add('billing_merchant_type', 'You cannot change the "Billing items links to" setting.');
                        });
                    }
                }
            }
        }
    }
}
