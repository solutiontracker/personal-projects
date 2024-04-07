<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgendaSpeakerlistRequest extends Model {

    use SoftDeletes;
    protected $table = 'conf_agenda_speakerlist_request';
    protected $fillable = ['event_id', 'agenda_id', 'attendee_id', 'session_id'];
    protected $dates = ['deleted_at'];

}