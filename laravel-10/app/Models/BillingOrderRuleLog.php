<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingOrderRuleLog extends Model {
    use SoftDeletes;
    protected $table = 'conf_billing_order_rule_log';
    protected $fillable = ['rule_id','order_id','item_id','item_qty','rule_qty','discount_type','tem_price','rule_discount','item_discount'];
}