<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSpeakerlistLiveLog extends Model {

    use SoftDeletes;
    protected $table = 'conf_event_speakerlist_live_log';
    protected $fillable = ['event_id', 'agenda_id', 'attendee_id', 'live_date', 'start_time', 'end_time'];
    protected $dates = ['deleted_at'];

}