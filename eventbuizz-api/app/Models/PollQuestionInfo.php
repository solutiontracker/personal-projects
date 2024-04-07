<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PollQuestionInfo extends Model
{
    use SoftDeletes;
    protected $table = 'conf_poll_question_info';
    protected $fillable = ['name', 'value', 'question_id', 'languages_id'];
    protected $dates = ['deleted_at'];
}