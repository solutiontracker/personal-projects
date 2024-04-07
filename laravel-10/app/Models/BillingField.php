<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingField extends Model {

    use SoftDeletes;
    protected $table = 'conf_billing_fields';
    protected $fillable = ['sort_order','event_id','status','field_alias','sort_order','mandatory','section_alias','type', 'billing_group_id', 'registration_form_id'];

    public function info()
    {
        return $this->hasMany('\App\Models\BillingFieldInfo', 'field_id');
    }
}