<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeSurveyResultScore extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_attendee_survey_results_score';
    protected $fillable = ['score', 'survey_id', 'question_id', 'attendee_id', 'event_id',  'status'];
}