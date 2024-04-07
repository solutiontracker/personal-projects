<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeSurveyQuestionInfo extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_attendee_survey_questions_info';
    protected $fillable = ['name', 'value', 'question_id', 'languages_id'];
}