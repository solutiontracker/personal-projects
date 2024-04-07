<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventModuleOrder extends Model {

    use SoftDeletes;
    protected $table = 'conf_event_modules_order';
    protected $fillable = ['sort_order','event_id','status','alias','icon','is_purchased','group','version','type'];

    public function info()
    {
        return $this->hasMany('\App\Models\ModuleOrderInfo', 'module_order_id');
    }
}