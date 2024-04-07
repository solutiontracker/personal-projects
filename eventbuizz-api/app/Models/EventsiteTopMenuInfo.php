<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventsiteTopMenuInfo extends Model
{
    use SoftDeletes;
    protected $table = 'conf_eventsite_modules_order_info';
    protected $fillable = ['id','name', 'value', 'languages_id','status','module_order_id','created_at','updated_at','deleted_at'];

    protected $dates = ['deleted_at'];

    public function moduleOrder()
    {
        return $this->belongsTo('ModuleOrder','module_order_id','id');
    }
}
