<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventDataLogSession extends Model {
	use SoftDeletes;
    protected $table = 'conf_event_data_log_session';
    protected $fillable = ['session_id', 'session_expires', 'session_data', 'delete_test', 'login_time', 'login_update', 'logout_time', 'ip_address', 'operating_system', 'device_type', 'browser_type', 'browser_version', 'user_agent', 'event_id', 'attendee_id'];
}