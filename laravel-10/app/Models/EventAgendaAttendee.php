<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAgendaAttendee extends Model {

    protected $table = 'conf_event_agenda_attendees';
    protected $fillable = ['attendee_id','agenda_id','fav'];

    public function program()
    {
        return $this->belongsTo('Program','agenda_id','id');
    }

}