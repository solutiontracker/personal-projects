<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventMeetingHistory extends Model
{
    protected $table = 'conf_event_meetings_history';
    protected $fillable = ['event_id', 'attendee_id', 'plateform', 'channel', 'video', 'audio', 'share'];
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
