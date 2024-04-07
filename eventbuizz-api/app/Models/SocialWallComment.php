<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialWallComment extends Model
{
    use SoftDeletes;
    protected $table = 'conf_social_wall_comments';
    protected $fillable = ['id', 'post_id','attendee_id','parent_id','comment'];

}
