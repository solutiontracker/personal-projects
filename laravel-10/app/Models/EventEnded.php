<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventEnded extends Model {

    protected $table = 'conf_events_ended';
    protected $fillable = ['event_id','event_name','event_link','notification_date'];
}