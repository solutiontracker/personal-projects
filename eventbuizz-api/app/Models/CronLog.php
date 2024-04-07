<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CronLog extends Model
{

	use SoftDeletes;
	protected $table = 'conf_cron_log';
    protected $fillable = ['organizer_id','type','date'];
	protected $dates = ['deleted_at'];
}