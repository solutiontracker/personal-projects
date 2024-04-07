<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventPoll extends Model
{
	use SoftDeletes;
	protected $table = 'conf_event_polls';
    protected $fillable = ['event_id', 'sort_order', 'agenda_id', 'start_date', 'end_date', 'status'];
	protected $dates = ['deleted_at'];
}