<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeActivityLog extends Model {

	use SoftDeletes;

	protected $table = 'conf_attendee_activity_log';
	protected $fillable = [
	    'user_id',
        'event_id',
        'ip',
        'browser',
        'os',
        'platform',
        'history_type'
        ];
	protected $dates = ['created_at'];

}
