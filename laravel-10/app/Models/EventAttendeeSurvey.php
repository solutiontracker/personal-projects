<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeSurvey extends Model
{
	use SoftDeletes;
	protected $table = 'conf_event_attendee_surveys';
    protected $fillable = ['sort_order', 'user_type', 'user_id', 'event_id', 'attendee_id', 'status'];
	protected $dates = ['deleted_at'];

	public function info()
    {
        return $this->hasMany('\App\Models\EventAttendeeSurveyInfo','survey_id');
    }

    public function question()
    {
        return $this->hasMany('\App\Models\EventAttendeeSurveyQuestion','survey_id');
    }
    public function results()
    {
        return $this->hasMany('\App\Models\EventAttendeeSurveyResult','survey_id');
    }

    public function score()
    {
        return $this->hasMany('\App\Models\EventAttendeeSurveyResultScore','survey_id');
    }
}