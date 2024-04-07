<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSurveyAlert extends Model
{
    protected $table = 'conf_event_survey_alert';
    protected $fillable = ['survey_id','alert_id','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
