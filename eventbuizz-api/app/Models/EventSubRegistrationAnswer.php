<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSubRegistrationAnswer extends Model
{
    use SoftDeletes;

    protected $attributes = [
        'sort_order' => '0'
    ];

    protected $table = 'conf_event_sub_registration_answers';

    protected $fillable = ['sort_order', 'correct', 'question_id','link_to', 'status'];
    
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\EventSubRegistrationAnswerInfo', 'answer_id');
    }
}