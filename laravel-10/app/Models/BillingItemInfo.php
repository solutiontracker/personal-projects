<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingItemInfo extends Model {

    use SoftDeletes;
    protected $table = 'conf_billing_items_info';
    protected $fillable = ['name','value','item_id','languages_id'];
    protected $dates = ['deleted_at'];


}