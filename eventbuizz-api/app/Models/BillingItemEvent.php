<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingItemEvent extends Model {

    use SoftDeletes;
    protected $table = 'conf_billing_item_events';
    protected $fillable = ['status','item_id','event_id'];
    protected $dates = ['deleted_at'];


}