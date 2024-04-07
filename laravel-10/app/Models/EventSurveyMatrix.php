<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSurveyMatrix extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_survey_matrix';
    protected $fillable = ['name', 'sort_order', 'question_id'];
    protected $appends = ['value'];

    public function getValueAttribute(){
        return $this->attributes['name'];
    }
    
}
