<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeInviteStats extends Model
{
    use SoftDeletes;
    protected $table = 'conf_attendee_invite_stats';
    protected $fillable = ['organizer_id', 'event_id', 'template_alias', 'open', 'click', 'reject', 'send', 'deferral', 'hard_bounce', 'soft_bounce', 'email'];
}
