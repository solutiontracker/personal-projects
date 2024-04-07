<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventDateFormat extends Model {

    protected $table = 'conf_event_date_format';
    protected $fillable = ['event_id', 'language_id', 'date_format_id'];
}