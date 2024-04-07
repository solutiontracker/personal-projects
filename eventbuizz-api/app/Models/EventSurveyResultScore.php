<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSurveyResultScore extends Model
{

    use SoftDeletes;
    protected $table = 'conf_event_survey_results_score';
    protected $fillable = ['score', 'survey_id', 'attendee_id', 'event_id', 'question_id', 'status'];
    protected $dates = ['deleted_at'];

    public function attendee()
    {
        return $this->belongsTo(Attendee::class, 'attendee_id', 'id');
    }

}