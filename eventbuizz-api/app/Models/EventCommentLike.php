<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCommentLike extends Model
{
    protected $table = 'conf_event_comments_likes';
    protected $fillable = ['id','attendee_id','comment_id','event_id','status','created_at','updated_at','deleted_at'];

    use SoftDeletes;

}
