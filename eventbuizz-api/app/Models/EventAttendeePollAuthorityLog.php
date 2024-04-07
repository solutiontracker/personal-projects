<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeePollAuthorityLog extends Model
{
    protected $table = 'conf_event_attendee_poll_authority_log';
    protected $fillable = ['event_id','attendee_to','attendee_from','is_accepted','is_read_to','is_read_from'];

    use SoftDeletes;
}
