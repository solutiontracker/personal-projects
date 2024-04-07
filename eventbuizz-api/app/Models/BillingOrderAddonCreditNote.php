<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingOrderAddonCreditNote extends Model
{

    protected $table = 'conf_billing_order_addons_credit_notes';
    protected $fillable = ['order_id', 'credit_note_id', 'order_number', 'attendee_id', 'addon_id', 'name', 'price', 'qty', 'discount', 'parent', 'link_to', 'link_to_id', 'group_id'];
    public $timestamps = false;

    public function credit_note()
    {
        return $this->belongsTo('\App\Models\BillingOrderCreditNote', 'credit_note_id');
    }
}
