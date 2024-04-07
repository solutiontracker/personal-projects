<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialMediaFeed extends Model {

    use SoftDeletes;
    protected $table = 'conf_social_media_feed';
    protected $fillable = ['event_id', 'fb_javascript', 'fb_html', 'twitter_html', 'instagram_html'];
    protected $dates = ['deleted_at'];
}
