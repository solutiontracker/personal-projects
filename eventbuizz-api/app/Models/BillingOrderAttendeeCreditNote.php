<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingOrderAttendeeCreditNote extends Model
{
    protected $table = 'conf_billing_order_attendees_credit_notes';

    protected $fillable = ['order_id', 'credit_note_id', 'order_number', 'attendee_id', 'event_qty', 'event_discount'];

    use SoftDeletes;

    public function credit_note()
    {
        return $this->belongsTo('\App\Models\BillingOrderCreditNote', 'credit_note_id', 'id');
    }
}
