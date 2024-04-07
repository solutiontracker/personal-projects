<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventMessageRecipient extends Model
{
    protected $table = 'conf_event_message_recipients';
    protected $fillable = ['mid','seq','receiver','all_recipients','event_id','status'];


}
