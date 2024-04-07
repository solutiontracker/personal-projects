<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeSurveyResult extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_attendee_survey_results';
    protected $fillable = ['answer', 'comments', 'event_id', 'survey_id', 'attendee_id', 'question_id', 'answer_id', 'status', 'lead_id', 'device_id'];
    protected $dates = ['deleted_at'];

    public function attendee()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
    }

    public function lead(){
        return $this->belongsTo('\App\Models\EventLead', 'lead_id', 'id');
    }
}