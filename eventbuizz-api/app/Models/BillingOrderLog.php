<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingOrderLog extends Model
{
    use SoftDeletes;
    protected $table = 'conf_billing_order_log';
    protected $fillable = ['event_id', 'organizer_id', 'order_id', 'field_name', 'update_date', 'update_date_time', 'data_log'];
    protected $dates = ['deleted_at'];
}
