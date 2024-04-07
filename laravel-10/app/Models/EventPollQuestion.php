<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventPollQuestion extends Model
{

    use SoftDeletes;
    protected $table = 'conf_event_poll_questions';
    protected $fillable = ['question_type', 'result_chart_type', 'required_question', 'enable_comments', 'sort_order', 'start_date', 'end_date', 'poll_id', 'status', 'allow_attendee'];
    protected $dates = ['deleted_at'];
}