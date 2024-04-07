<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingVoucherItem extends Model {

    use SoftDeletes;
    protected $table = 'conf_billing_voucher_items';
    protected $fillable = ['discount_type','price','useage','item_id','item_type','voucher_id'];
    protected $dates = ['deleted_at'];


}