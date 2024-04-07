<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialWallCommentLike extends Model
{
    use SoftDeletes;

    protected $table = 'conf_social_wall_comment_likes';
    protected $fillable = ['id', 'comment_id','attendee_id', 'post_id'];
}
