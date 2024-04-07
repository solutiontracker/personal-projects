<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSiteModuleOrderInfo extends Model {

    protected $table = 'conf_eventsite_modules_order_info';
    protected $fillable = ['id','name', 'value', 'languages_id','status','module_order_id','created_at','updated_at','deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}