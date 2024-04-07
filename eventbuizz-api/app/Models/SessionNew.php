<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionNew extends Model {
    protected $table = 'conf_sessions_new';
    protected $fillable = ['attendee_id', 'user_email', 'event_id', 'site_type', 'ip_address', 'user_agent','payload','last_activity'];
}
