<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSurveyInfo extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_surveys_info';
    protected $fillable = ['name', 'value', 'languages_id', 'survey_id', 'status'];
    protected $dates = ['deleted_at'];
}
