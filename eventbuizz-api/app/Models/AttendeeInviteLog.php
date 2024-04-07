<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeInviteLog extends Model
{
    protected $table = 'conf_attendee_invites_log';
    protected $fillable = ['event_id', 'organizer_id', 'first_name', 'last_name', 'email', 'phone', 'email_sent',
        'sms_sent', 'not_sent', 'date_sent', 'type'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
