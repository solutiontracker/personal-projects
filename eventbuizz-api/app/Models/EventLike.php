<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventLike extends Model
{
    protected $table = 'conf_event_likes';
    protected $fillable = ['id','image_id','event_id','attendee_id','status','created_at','updated_at','deleted_at'];

    use SoftDeletes;

}
