<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubAdminEvent extends Model
{

	use SoftDeletes;
	protected $table = 'conf_subadmin_events';
    protected $fillable = ['id','event_id','admin_id'];
	protected $dates = ['deleted_at'];
}