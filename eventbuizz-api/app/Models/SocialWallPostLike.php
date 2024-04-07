<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialWallPostLike extends Model
{
    use SoftDeletes;

    protected $table = 'conf_social_wall_post_likes';
    protected $fillable = ['id', 'post_id','attendee_id'];
}
