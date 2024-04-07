<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventPollResultScore extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_poll_results_score';
    protected $fillable = ['score', 'event_id', 'question_id', 'attendee_id', 'status'];
    protected $dates = ['deleted_at'];
}