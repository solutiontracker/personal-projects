<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeBilling extends Model
{

    use SoftDeletes;

    protected $table = 'conf_attendee_billing';
    
    protected $fillable = ['organizer_id', 'event_id','order_id', 'attendee_id', 'billing_membership', 'billing_member_number', 'billing_bruger_id', 'billing_private_street', 'billing_private_house_number', 'billing_private_post_code', 'billing_private_city', 'billing_private_country', 'billing_company_type', 'billing_company_registration_number', 'billing_ean', 'billing_contact_person_name', 'billing_contact_person_email', 'billing_contact_person_mobile_number', 'billing_company_street', 'billing_company_house_number', 'billing_company_post_code', 'billing_company_city', 'billing_company_country', 'billing_poNumber', 'invoice_reference_no', 'billing_company_invoice_payer_street_house_number', 'billing_company_invoice_payer_company_name', 'billing_company_invoice_payer_post_code', 'billing_company_invoice_payer_city', 'billing_company_invoice_payer_country', 'billing_company_street_2', 'billing_company_state', 'created_at', 'updated_at', 'deleted_at'];

    protected $dates = ['deleted_at'];

    public function country()
    {
        return $this->belongsTo(Country::class, 'billing_company_country');
    }
}