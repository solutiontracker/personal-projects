<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventNativeAppModule extends Model {

    protected $table = 'conf_event_native_app_modules';
    protected $fillable = ['sort','event_id','status','module_alias'];
}