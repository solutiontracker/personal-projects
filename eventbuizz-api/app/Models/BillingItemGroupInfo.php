<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingItemGroupInfo extends Model {

    use SoftDeletes;
    protected $table = 'conf_billing_item_group_info';
    protected $fillable = ['name','value','group_id','languages_id'];
    protected $dates = ['deleted_at'];


}