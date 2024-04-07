<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeSurveyInfo extends Model
{
	use SoftDeletes;
	protected $table = 'conf_event_attendee_surveys_info';
    protected $fillable = ['name', 'value', 'languages_id', 'survey_id'];
	protected $dates = ['deleted_at'];
}