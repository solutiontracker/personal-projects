<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeAuthentication extends Model
{

    use SoftDeletes;
    protected $table = 'conf_attendee_authentication';
    protected $fillable = ['email', 'token', 'type', 'expire_at', 'to', 'refrer', 'event_id'];
    protected $dates = ['deleted_at'];
}
