<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventChatThreadParticipant extends Model
{
    protected $table = 'conf_event_chat_thread_participant';
    protected $fillable = ['thread_id','user_id'];
    public $timestamps = false;
}
