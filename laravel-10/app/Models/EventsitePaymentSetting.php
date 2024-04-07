<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventsitePaymentSetting extends Model
{
    protected $attributes = [
        'swed_bank_region' => '',
        'dibs_hmac' => '',
        'swed_bank_language' => '',
        'swed_bank_password' => '',
        'SecretKey' => '',
        'eventsite_vat' => '0',
        'eventsite_vat_countries' => '',
        'eventsite_invoice_no' => '0',
        'eventsite_invoice_prefix' => '',
        'eventsite_invoice_currentnumber' => '0',
        'eventsite_order_prefix' => '',
        'billing_merchant_type' => '0',
        'billing_yourpay_language' => '',
        'payment_terms' => '',
        'footer_text' => '',
        'invoice_logo' => '',
        'bcc_emails' => '',
        'auto_invoice' => '0',
        'account_number' => '',
        'bank_name' => '',
        'payment_date' => '',
        'max_billing_item_quantity' => '0',
        'show_business_dating' => '0',
        'show_subregistration' => '0',
        'show_qty_label_free' => '0',
        'hotel_from_date' => '0000-00-00',
        'hotel_to_date' => '0000-00-00',
        'publicKey' => '',
        'privateKey' => '',
        'mistertango_markets' => '',
        'qty_from_date' => '0000-00-00',
        'qp_agreement_id' => '',
        'qp_secret_key' => '',
        'wc_customer_id' => '',
        'wc_secret' => '',
        'wc_shop_id' => '',
        'stripe_api_key' => '',
        'stripe_secret_key' => '',
        'eventsite_currency' => '0',
        'maintain_quantity' => '0',
        'maintain_quantity_item' => '0',
        'is_voucher' => '0',
        'billing_type' => '0',
        'invoice_dimensions' => '0',
        'eventsite_billing' => '0',
        'admin_fee_status' => '0',
        'eventsite_enable_billing_item_desc' => '0',
        'billing_item_type' => '0',
        'eventsite_billing_detail' => '0',
        'eventsite_always_apply_vat' => '0',
        'eventsite_show_email_in_invoice' => '0',
        'show_hotels' => '0',
        'hotel_vat_status' => '0',
        'hotel_vat' => '0',
        'hotel_person' => '0',
        'hotel_currency' => '0',
        'show_qty_label_free' => '0',
        'use_qty_rules' => '0',
    ];

    protected $table = 'conf_eventsite_payment_settings';

    protected $fillable = ['event_id','eventsite_merchant_id','swed_bank_password','swed_bank_region','swed_bank_language','SecretKey','eventsite_currency','eventsite_vat','eventsite_invoice_no','eventsite_invoice_prefix','maintain_quantity','maintain_quantity_item','is_voucher','billing_type','payment_terms','footer_text','invoice_dimensions','invoice_logo','eventsite_billing','admin_fee_status','eventsite_enable_billing_item_desc', 'auto_invoice', 'account_number', 'bank_name', 'payment_date','billing_item_type','eventsite_billing_detail','eventsite_always_apply_vat','eventsite_vat_countries','eventsite_send_email_order_creator','max_billing_item_quantity','show_business_dating','show_subregistration','eventsite_show_email_in_invoice','show_hotels','hotel_vat_status','hotel_vat','hotel_person','hotel_from_date','hotel_to_date','hotel_currency', 'publicKey', 'privateKey','show_qty_label_free','mistertango_markets','eventsite_invoice_currentnumber','qty_from_date','use_qty_rules','qp_agreement_id','qp_secret_key','qp_auto_capture','wc_customer_id', 'wc_secret', 'wc_shop_id','stripe_api_key','stripe_secret_key', 'show_hotel_prices', 'bambora_secret_key', 'is_item', 'registration_form_id', 'show_items'];

}
