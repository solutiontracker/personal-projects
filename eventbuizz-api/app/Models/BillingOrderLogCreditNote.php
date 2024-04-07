<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingOrderLogCreditNote extends Model {
    use SoftDeletes;
    protected $table = 'conf_billing_order_log_credit_notes';
    protected $fillable = ['event_id','organizer_id','order_id','credit_note_id','order_number','field_name','update_date','update_date_time','data_log'];
}