<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAgendaSpeakerlistSession extends Model {
    use SoftDeletes;
    protected $table = 'conf_event_agenda_speakerlist_session';
    protected $fillable = ['event_id', 'agenda_id', 'is_active', 'session_date', 'start_time', 'end_time'];
    protected $dates = ['deleted_at'];
}