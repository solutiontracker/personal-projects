<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialMediaFeedSetting extends Model {

    use SoftDeletes;
    protected $table = 'conf_social_media_feed_settings';
    protected $fillable = ['event_id', 'hash_label', 'background_color', 'background_image', 'refresh_time'];
    protected $dates = ['deleted_at'];
}
