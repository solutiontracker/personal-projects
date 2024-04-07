<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingFieldInfo extends Model {

    protected $table = 'conf_billing_fields_info';
    protected $fillable = ['name', 'value', 'languages_id','status','field_id'];

}