<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeSurveyAnswerInfo extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_attendee_survey_answers_info';
    protected $fillable = ['name', 'value', 'answer_id', 'question_id','languages_id'];
}