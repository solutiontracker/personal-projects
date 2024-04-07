<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSetting extends Model
{

    protected $table = 'conf_event_settings';
    protected $fillable = ['name', 'value', 'event_id'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}