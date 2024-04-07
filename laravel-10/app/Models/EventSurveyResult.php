<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSurveyResult extends Model
{

    use SoftDeletes;
    protected $table = 'conf_event_survey_results';
    protected $fillable = ['answer', 'comment', 'event_id', 'survey_id', 'question_id', 'answer_id', 'attendee_id', 'status'];
    protected $dates = ['deleted_at'];
}