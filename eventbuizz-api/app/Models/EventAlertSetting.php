<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventAlertSetting extends Model
{
    protected $table = 'conf_event_alert_settings';

    protected $fillable = ['event_id', 'display_in_dashboard'];

    use SoftDeletes;
}
