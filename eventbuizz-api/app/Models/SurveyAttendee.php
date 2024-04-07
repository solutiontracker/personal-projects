<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyAttendee extends Model
{

	use SoftDeletes;
	protected $table = 'conf_survey_attendees';
    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'company_name', 'attendee_id', 'survey_id', 'status'];
	protected $dates = ['deleted_at'];


}