<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingItemGroup extends Model {

    use SoftDeletes;
    protected $table = 'conf_billing_item_group';
    protected $fillable = ['event_id','sort_order','group_type','organizer_id','status'];
    protected $dates = ['deleted_at'];
}