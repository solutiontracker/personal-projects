<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventChatMessageReadState extends Model
{
    protected $table = 'conf_event_chat_message_read_state';
    protected $fillable = ['message_id','user_id','read_date'];
    public $timestamps = false;
}
