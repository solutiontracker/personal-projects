<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleGroupInfo extends Model {
    protected $table = 'conf_modules_groups_info';
    protected $fillable = ['id','name', 'value', 'languages_id', 'group_id','created_at','updated_at'];
}

