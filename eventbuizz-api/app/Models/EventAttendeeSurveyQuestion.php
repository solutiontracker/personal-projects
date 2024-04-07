<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeSurveyQuestion extends Model
{
	use SoftDeletes;
	protected $table = 'conf_event_attendee_survey_questions';
    protected $fillable = ['question_type', 'result_chart_type', 'anonymous', 'required_question', 'enable_comments', 'sort_order', 'start_date', 'end_date', 'survey_id', 'status'];

	public function info()
	{
		return $this->hasMany('\App\Models\EventAttendeeSurveyQuestionInfo', 'question_id');
	}

	public function answer()
	{
		return $this->hasMany('\App\Models\EventAttendeeSurveyAnswer', 'question_id');
	}

    public function attendee_survey()
    {
        return $this->belongsTo('\App\Models\EventAttendeeSurvey', 'survey_id', 'id');
    }

    public function matrix()
    {
        return $this->hasMany('\App\Models\EventHubSurveyMatrix', 'question_id');
    }

}