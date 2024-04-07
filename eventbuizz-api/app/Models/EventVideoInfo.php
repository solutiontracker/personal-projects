<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventVideoInfo extends Model
{
    protected $table = 'conf_event_videos_info';
    protected $fillable = ['id', 'name', 'value', 'video_id', 'languages_id', 'status', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
}
