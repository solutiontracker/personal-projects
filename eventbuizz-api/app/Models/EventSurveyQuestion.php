<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSurveyQuestion extends Model
{

	use SoftDeletes;
	protected $attributes = [
		'start_date' => '0000-00-00 00:00:00',
		'end_date' => '0000-00-00 00:00:00',
	];
	protected $table = 'conf_event_survey_questions';
    protected $fillable = ['question_type', 'result_chart_type', 'anonymous', 'required_question', 'enable_comments', 'sort_order', 'start_date', 'end_date', 'survey_id', 'status', 'max_options', 'is_anonymous', 'min_options', 'allow_attendee'];
	protected $dates = ['deleted_at'];

	public function info()
	{
		return $this->hasMany('\App\Models\SurveyQuestionInfo', 'question_id');
	}

	public function answer()
	{
		return $this->hasMany('\App\Models\EventSurveyAnswer', 'question_id');
	}

	public function matrix(){
	    return $this->hasMany(EventSurveyMatrix::class, 'question_id');
    }

	public function results()
	{
		return $this->hasMany('\App\Models\EventSurveyResult', 'question_id');
	}
	
	public function resultScore()
	{
		return $this->hasMany('\App\Models\EventSurveyResultScore', 'question_id');
	}
}