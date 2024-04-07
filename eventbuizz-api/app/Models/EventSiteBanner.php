<?php

namespace App\Models;

use App\Scopes\EventScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class EventSiteBanner extends Model
{
    use SoftDeletes;
    protected $attributes = [
        'banner_type' => 'top',
        'video_type' => '1',
        'video_duration' => '',
        'status' => '1',
        'sort_order' => '0'
    ];
    protected $table = 'conf_eventsite_banners';

    protected $fillable = ['event_id', 'banner_type', 'video_type', 'video_duration', 'image', 'sort_order', 'status','title_color','sub_title_color'];

    public function info()
    {
        return $this->hasMany(EventSiteBannerInfo::class, 'banner_id');
    }

}
