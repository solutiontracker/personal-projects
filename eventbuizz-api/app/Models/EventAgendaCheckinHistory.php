<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAgendaCheckinHistory extends Model {

    use SoftDeletes;
    protected $table = 'conf_event_agenda_checkin_history';
    protected $fillable = ['event_id', 'agenda_id', 'session_id', 'attendee_id'];
    protected $dates = ['deleted_at'];
}