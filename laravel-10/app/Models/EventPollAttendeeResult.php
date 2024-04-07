<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventPollAttendeeResult extends Model
{
    protected $table = "conf_event_poll_attendees_results";
    protected $fillable = ['event_id','attendee_id', 'poll_id','question_id','agenda_id'];

    use SoftDeletes;
}
