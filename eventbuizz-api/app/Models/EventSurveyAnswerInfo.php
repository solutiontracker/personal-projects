<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSurveyAnswerInfo extends Model
{
    use SoftDeletes;
    protected $attributes = [
        'question_id' => '0',
    ];
    protected $table = 'conf_event_survey_answers_info';
    protected $fillable = ['name', 'value', 'answer_id', 'question_id','languages_id', 'status'];
    protected $dates = ['deleted_at'];

}