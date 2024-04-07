<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventModuleTabSettings extends Model {

    protected $table = 'conf_event_module_tab_settings';
    protected $fillable = ['event_id','tab_name','status','sort_order','module'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function event()
    {
        return $this->belongsTo('Events', 'event_id', 'id');
    }
}