<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrintLog extends Model
{
    protected $table = 'conf_print_log';
    protected $fillable = ['event_id', 'attendee_id', 'posted_data', 'message', 'status'];

}
