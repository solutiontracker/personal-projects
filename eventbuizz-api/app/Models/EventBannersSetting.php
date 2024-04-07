<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventBannersSetting extends Model
{
    protected $table = 'conf_event_banners_settings';
    protected $fillable = ['event_id','main_banner_position', 'native_banner_position', 'bannerads_orderby'];
}
