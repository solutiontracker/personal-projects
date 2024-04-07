<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventChatThread extends Model
{
    protected $table = 'conf_event_chat_thread';
    protected $fillable = ['id','event_id'];
    public $timestamps = false;

    public function participants()
    {
        return $this->hasMany(EventChatThreadParticipant::class, 'thread_id');
    }


}
