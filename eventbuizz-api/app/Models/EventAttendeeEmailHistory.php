<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeEmailHistory extends Model
{
    protected $table = 'conf_event_attendees_email_history';
    protected $fillable = ['event_id', 'attendee_id', 'email_date'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
