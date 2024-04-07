<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventMapSetting extends Model
{
    protected $table = 'conf_event_map_setting';
    protected $fillable = ['event_id','name','value','status'];
}
