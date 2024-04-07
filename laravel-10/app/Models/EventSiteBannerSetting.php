<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSiteBannerSetting extends Model
{
    protected $table = 'conf_eventsite_banner_settings';
    protected $fillable = ['event_id', 'title', 'caption', 'register_button', 'bottom_bar','rotation_interval'];
}
