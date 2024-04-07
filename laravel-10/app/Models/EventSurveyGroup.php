<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSurveyGroup extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_surveys_groups';
    protected $fillable = ['survey_id', 'group_id'];
}