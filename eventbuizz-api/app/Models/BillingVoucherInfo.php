<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingVoucherInfo extends Model {

    use SoftDeletes;
    protected $table = 'conf_billing_vouchers_info';
    protected $fillable = ['name','value','voucher_id','languages_id'];
    protected $dates = ['deleted_at'];


}