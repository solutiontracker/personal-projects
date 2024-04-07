<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgoraCallAnalytics extends Model
{
    use SoftDeletes;
    protected $table = 'conf_agora_call_analytics';

    protected $fillable = ['id', 'event_id', 'attendee_id', 'agora_id', 'vid', 'project_id', 'created_ts', 'destroyed_ts', 'cname', 'cid', 'finished', 'ts', 'mode', 'duration', 'permanented', 'created_ts_at', 'destroyed_ts_at', 'ts_at'];

}
