<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NativeAppUpdate extends Model
{
	use SoftDeletes;
	protected $table = 'conf_native_app_updates';
    protected $fillable = ['id', 'event_id','module','checked_date','created_at','updated_at'];
	protected $dates = ['deleted_at'];
}