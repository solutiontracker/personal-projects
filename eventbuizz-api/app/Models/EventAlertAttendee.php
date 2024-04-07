<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAlertAttendee extends Model {
    protected $table = 'conf_event_alert_attendees';
    protected $fillable = ['date', 'attendee_id', 'alert_id', 'status'];
}