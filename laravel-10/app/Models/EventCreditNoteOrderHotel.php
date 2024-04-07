<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCreditNoteOrderHotel extends Model {
    protected $table = 'conf_event_credit_note_order_hotels';
    protected $fillable = ['hotel_id', 'order_id', 'name', 'price', 'price_type', 'vat', 'vat_price', 'rooms', 'checkin', 'checkout', 'created_at','updated_at', 'deleted_at'];
}

