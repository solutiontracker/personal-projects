<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSubRegistrationAnswerInfo extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_sub_registration_answer_info';
    protected $fillable = ['name', 'value', 'answer_id', 'languages_id', 'status'];
    protected $dates = ['deleted_at'];
}