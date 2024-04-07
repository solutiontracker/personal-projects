<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PollLog extends Model
{
    protected $table = 'conf_polls_log';
    public $timestamps = false;
    protected $fillable = ['event_id','attendee_id', 'poll_id', 'status', 'created_at'];
}
