<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventNameBadgeAttendee extends Model
{
    protected $table = 'conf_event_name_badge_attendees';
    public $timestamps = false;
}
