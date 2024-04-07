<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventHubSurveyAttendeeResult extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_hub_survey_attendees_results';
    protected $fillable = ['event_id','attendee_id', 'survey_id','question_id', 'lead_id', 'device_id'];
    protected $dates = ['deleted_at'];
}