<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSmsHistoryInvite extends Model {
    protected $table = 'conf_event_sms_history_invite';
    protected $fillable = ['organizer_id', 'event_id', 'name', 'email', 'phone', 'status', 'status_msg', 'sms', 'date_sent', 'type'];
}
