<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingOrderCreditNote extends Model
{

    use SoftDeletes;
    protected $table = 'conf_billing_orders_credit_notes';
    protected $fillable = ['order_id', 'event_id', 'attendee_id', 'session_id', 'language_id', 'is_free', 'e_invoice', 'event_discount', 'security', 'vat', 'vat_amount', 'transaction_id', 'invoice_reference_no', 'grand_total', 'summary_sub_total', 'total_attendee', 'discount_type', 'code', 'coupon_id', 'discount_amount', 'order_date', 'eventsite_currency', 'order_number', 'billing_quantity', 'status', 'comments', 'is_voucher', 'is_payment_received', 'payment_received_date', 'order_type', 'dibs_dump', 'hide_first_billing_item_description', 'credit_note_create_date'];
    protected $dates = ['deleted_at'];

    public function order_attendee()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
    }

    public function credit_note_order_attendees()
    {
        return $this->hasMany('\App\Models\BillingOrderAttendeeCreditNote', 'credit_note_id');
    }

    public function order_attendees()
    {
        return $this->hasMany('\App\Models\BillingOrderAttendeeCreditNote', 'credit_note_id');
    }

    public function order_addons($attendee_id = null)
    {
        if ($attendee_id) {
            return $this->hasMany('\App\Models\BillingOrderAddonCreditNote', 'credit_note_id')->where('conf_billing_order_addons_credit_notes.attendee_id', '=', $attendee_id);
        }
        return $this->hasMany('\App\Models\BillingOrderAddonCreditNote', 'credit_note_id');
    }

    public function order_hotels()
    {
        return $this->hasMany('\App\Models\EventOrderHotel', 'order_id', 'order_id');
    }

    public function event()
    {
        return $this->belongsTo('\App\Models\Event', 'event_id');
    }

    public function order_address()
    {
        return $this->hasOne('\App\Models\AttendeeBilling', 'order_id');
    }

    public function order_vats()
    {
        return $this->hasMany('\App\Models\BillingOrderVAT', 'order_id', 'order_id');
    }
}
