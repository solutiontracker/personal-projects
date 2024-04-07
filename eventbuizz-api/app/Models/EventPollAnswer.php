<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventPollAnswer extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_poll_answers';
    protected $fillable = ['sort_order', 'correct', 'question_id', 'status'];
    protected $dates = ['deleted_at'];
}