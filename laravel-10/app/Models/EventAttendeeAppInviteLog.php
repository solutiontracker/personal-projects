<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeAppInviteLog extends Model
{
    protected $table = 'conf_event_attendees_app_invite_log';
    protected $fillable = ['event_id', 'attendee_id', 'email_sent', 'sms_sent', 'email_date'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
