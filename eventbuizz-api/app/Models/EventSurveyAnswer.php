<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSurveyAnswer extends Model
{
    use SoftDeletes;
    protected $attributes = [
        'sort_order' => '0',
    ];
    protected $table = 'conf_event_survey_answers';
    protected $fillable = ['answer', 'sort_order', 'correct', 'question_id', 'status'];
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\EventSurveyAnswerInfo', 'answer_id');
    }

    public function result()
    {
        return $this->hasMany('\App\Models\EventSurveyResult', 'answer_id');
    }
}