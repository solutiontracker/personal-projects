<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventPollResult extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_poll_results';
    protected $fillable = ['answer', 'comments', 'question_id', 'answer_id', 'event_id', 'poll_id', 'agenda_id', 'attendee_id', 'status'];
    protected $dates = ['deleted_at'];
}