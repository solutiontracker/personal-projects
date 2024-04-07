<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CrashLog extends Model
{
	use SoftDeletes;
    
    protected $connection = "mysql-logs";

	protected $table = 'conf_crash_log';

    protected $fillable = ['body'];

	protected $dates = ['deleted_at'];
}