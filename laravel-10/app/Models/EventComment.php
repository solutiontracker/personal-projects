<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventComment extends Model
{
    protected $table = 'conf_event_comments';
    protected $fillable = ['id','comment','event_id','attendee_id','parent_id','image_id','status','created_at','updated_at','deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
