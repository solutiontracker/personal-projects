<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventsiteTopMenu extends Model
{
    use SoftDeletes;
    protected $table = 'conf_eventsite_modules_order';
    protected $fillable = ['sort_order','event_id','status','alias','version','created_at','updated_at','icon','is_purchased'];

    public function info()
    {
        return $this->hasMany(EventsiteTopMenuInfo::class,'module_order_id');
    }
}
