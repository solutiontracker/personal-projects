<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingOrderVAT extends Model
{

    use SoftDeletes;

    protected $table = 'conf_billing_order_vats';

    protected $fillable = ['order_id', 'vat', 'vat_price'];

}
