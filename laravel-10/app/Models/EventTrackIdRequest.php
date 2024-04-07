<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTrackIdRequest extends Model
{
    protected $table = 'conf_event_trackid_request';
    protected $fillable = ['event_id','organizer_id','status','read'];
}
