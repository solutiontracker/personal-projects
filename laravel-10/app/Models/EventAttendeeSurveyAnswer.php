<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeSurveyAnswer extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_attendee_survey_answers';
    protected $fillable = ['sort_order', 'question_id', 'correct', 'status'];

    public function info()
	{
		return $this->hasMany('\App\Models\EventAttendeeSurveyAnswerInfo', 'answer_id');
	}
}