<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventSubRegistrationResult extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_sub_registration_results';

    protected $fillable = ['event_id', 'sub_registration_id', 'answer', 'comments', 'answer_id', 'question_id', 'attendee_id', 'update_itration', 'result_clear_admin'];

    public $timestamps = true;

    protected $dates = ['deleted_at'];

}