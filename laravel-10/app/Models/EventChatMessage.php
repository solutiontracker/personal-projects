<?php


    namespace App\Models;


    use Illuminate\Database\Eloquent\Model;

    class EventChatMessage extends Model
    {
        public $timestamps = false;
        protected $table = 'conf_event_chat_message';
        protected $fillable = ['thread_id','sent_date','body','sender_id'];
    }