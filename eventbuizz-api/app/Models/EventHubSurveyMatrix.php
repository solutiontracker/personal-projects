<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventHubSurveyMatrix extends Model
{
	use SoftDeletes;
    protected $table = 'conf_event_hub_survey_matrix';
    protected $fillable = ['name', 'question_id','sort_order'];
    protected $dates = ['deleted_at'];
}