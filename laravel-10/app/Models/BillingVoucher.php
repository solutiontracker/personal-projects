<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingVoucher extends Model
{

    use SoftDeletes;
    protected $table = 'conf_billing_vouchers';
    protected $fillable = ['type', 'discount_type', 'price', 'expiry_date', 'usage', 'event_id', 'status', 'code','registration_form_id'];
    

    public function info()
    {
        return $this->hasMany('\App\Models\BillingVoucherInfo', 'voucher_id');
    }

    public function items()
    {
        return $this->hasMany('\App\Models\BillingVoucherItem', 'voucher_id');
    }

    public function orders()
    {
        return $this->hasMany('\App\Models\BillingOrder', 'coupon_id');
    }
}
