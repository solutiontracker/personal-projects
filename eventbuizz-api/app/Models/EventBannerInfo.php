<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventBannerInfo extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_banners_info';
    protected $fillable = ['name','value','languages_id','banner_id','status'];
}



