<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginDetailLog extends Model
{
    protected $table = 'conf_login_detail_log';
    protected $fillable = ['attendee_id','event_id','organizer_id', 'disclaimer_id', 'login_date', 'disclaimer_date', 'device', 'ip_address', 'disclaimer_version'];
}

