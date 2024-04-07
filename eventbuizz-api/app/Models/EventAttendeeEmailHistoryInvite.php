<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeEmailHistoryInvite extends Model
{
    protected $table = 'conf_event_attendees_email_history_invite';
    protected $fillable = ['event_id','email','email_date','invitation_accepted'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
