<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrintSelfCheckIn extends Model
{
    protected $table = 'conf_print_self_checkin';
    protected $fillable = ['event_id', 'active', 'code', 'title', 'description'];
}
