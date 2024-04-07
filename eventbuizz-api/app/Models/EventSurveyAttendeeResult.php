<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSurveyAttendeeResult extends Model
{
	use SoftDeletes;
	protected $table = 'conf_event_survey_attendees_results';
    protected $fillable = ['event_id', 'attendee_id', 'survey_id', 'question_id'];
	protected $dates = ['deleted_at'];
}