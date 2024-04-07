<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingItemRule extends Model
{
    use SoftDeletes;
    protected $table = 'conf_billing_item_rules';
    protected $fillable = ['id', 'item_id', 'rule_type', 'discount_type', 'discount', 'price', 'event_id', 'start_date', 'end_date', 'qty'];
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\BillingItemRuleInfo', 'rule_id');
    }
}
