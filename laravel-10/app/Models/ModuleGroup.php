<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleGroup extends Model {

    protected $table = 'conf_modules_groups';
    protected $fillable = ['id','event_id', 'alies', 'status','created_at','updated_at'];

	use SoftDeletes;

}

