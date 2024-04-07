<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendeeDeletionLog extends Model
{
    protected $table = 'conf_attendee_deletion_log';
    protected $fillable = ['event_id', 'attendee_id', 'order_id', 'additional_attendee', 'date'];
}
