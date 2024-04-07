<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventPollGroup extends Model
{
    protected $table = 'conf_event_poll_groups';
    protected $fillable = ['poll_id', 'group_id'];
}