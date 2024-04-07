<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NativeAppSettings extends Model {
    use SoftDeletes;

    protected $table = 'conf_event_native_app_settings';
    protected $fillable = ['event_id','event_location', 'event_date'];
}