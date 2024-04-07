<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSiteBannerInfo extends Model
{
    protected $table = 'conf_eventsite_banner_info';
    protected $fillable = ['name', 'value', 'banner_id', 'languages_id', 'status'];

    use SoftDeletes;
}
