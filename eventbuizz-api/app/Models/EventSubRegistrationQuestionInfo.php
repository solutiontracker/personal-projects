<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSubRegistrationQuestionInfo extends Model
{

    use SoftDeletes;
    protected $table = 'conf_event_sub_registration_question_info';
    protected $fillable = ['name', 'value', 'question_id', 'languages_id', 'status'];
    protected $dates = ['deleted_at'];


}