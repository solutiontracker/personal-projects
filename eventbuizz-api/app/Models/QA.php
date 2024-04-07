<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QA extends Model
{
    protected $table = 'conf_qa';
    protected $fillable = ['answered', 'show_projector', 'q_startTime', 'isStart', 'displayed', 'sort_order', 'attendee_id', 'event_id', 'agenda_id', 'speaker_id', 'anonymous_user', 'like_count'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
