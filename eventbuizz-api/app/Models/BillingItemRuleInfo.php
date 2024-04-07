<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingItemRuleInfo extends Model {

	protected $table = 'conf_billing_item_rules_info';
	protected $fillable = ['name','value','rule_id','languages_id','status'];

}