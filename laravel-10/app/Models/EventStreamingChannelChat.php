<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventStreamingChannelChat extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_streaming_channel_chat';
    protected $fillable = ['event_id', 'agenda_id', 'attendee_id', 'message', 'organizer_id', 'sendBy', 'ChannelName'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
