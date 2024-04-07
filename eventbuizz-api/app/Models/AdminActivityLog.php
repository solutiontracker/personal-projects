<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminActivityLog extends Model {

	use SoftDeletes;
	protected $table = 'conf_admin_activity_log';
    protected $fillable = [
        'user_id',
        'ip',
        'browser',
        'os',
        'history_type'
    ];
    protected $dates = ['created_at'];

}
