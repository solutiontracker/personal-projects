<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSurvey extends Model
{

	use SoftDeletes;
	protected $attributes = [
		'start_date' => '0000-00-00 00:00:00',
		'end_date' => '0000-00-00 00:00:00',
		'is_anonymous' => '0',
	];
	protected $table = 'conf_event_surveys';
    protected $fillable = ['start_date', 'end_date', 'event_id', 'status', 'is_anonymous', 'allow_attendee'];
	protected $dates = ['deleted_at'];

	public function info()
	{
		return $this->hasMany('\App\Models\EventSurveyInfo', 'survey_id');
	}

	public function question()
	{
		return $this->hasMany('\App\Models\EventSurveyQuestion', 'survey_id');
	}

	public function results()
	{
		return $this->hasMany('\App\Models\EventSurveyResult', 'survey_id');
	}

	public function score()
	{
		return $this->hasMany('\App\Models\EventSurveyResultScore', 'survey_id');
	}

	public function groups()
	{
		return $this->hasMany('\App\Models\EventSurveyGroup', 'survey_id', 'id');
	}

	public function surveyGroups(){
	    return $this->belongsToMany(EventSurveyGroup::class, 'conf_event_surveys_groups', 'survey_id', 'group_id');
    }

	public function m_campaign()
	{
		return $this->morphMany('\App\Models\TemplateCampaign', 'l_t');
	}
}