<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSmsHistory extends Model
{
    protected $table = 'conf_event_sms_history';
    protected $fillable = ['event_id', 'organizer_id', 'attendee_id', 'name', 'email', 'phone', 'status', 'status_msg', 'sms',
        'date_sent', 'type'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
