<?php

namespace App\Models;

use App\Scopes\EventScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class EventSiteModuleOrder extends Model {
    use SoftDeletes;
    protected $attributes = [
        'icon' => '',
    ];
    protected $table = 'conf_eventsite_modules_order';
    protected $fillable = ['sort_order','event_id','status','alias','version','created_at','updated_at','icon','is_purchased'];

    public function info()
    {
        return $this->hasMany('\App\Models\EventSiteModuleOrderInfo', 'module_order_id');
    }


}