<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PollSetting extends Model
{
    protected $attributes = [
        'tab' => '',
        'alerts' => '',
        'font_size' => '',
        'user_settings' => '',
    ];
    protected $table = 'conf_poll_settings';
    protected $fillable = ['event_id', 'tab', 'alerts', 'user_settings', 'display_poll', 'display_survey', 'tagcloud_shape', 'tagcloud_colors', 'projector_refresh_time', 'font_size', 'display_percentage','projector_setting','end_date','end_time'];
}