<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeImage extends Model {

    protected $table = 'conf_event_attendee_images';
    protected $fillable = ['event_id','attendee_id'];

}