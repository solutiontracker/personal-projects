<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventPollMatrix extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_poll_matrix';
    protected $fillable = ['name', 'sort_order', 'question_id'];
}
