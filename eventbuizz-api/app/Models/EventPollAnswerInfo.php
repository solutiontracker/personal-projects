<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventPollAnswerInfo extends Model
{

    use SoftDeletes;
    protected $table = 'conf_event_poll_answers_info';
    protected $fillable = ['name', 'value', 'answer_id', 'question_id', 'languages_id'];
    protected $dates = ['deleted_at'];

}