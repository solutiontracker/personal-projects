<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialWallPost extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_social_wall_posts';
    protected $fillable = ['id', 'event_id', 'attendee_id', 'content', 'image', 'image_height', 'image_width', 'type', 'likes_count', 'comments_count'];
    protected $dates = ['deleted_at'];
}
