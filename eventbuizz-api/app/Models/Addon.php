<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Addon extends Model {

    use SoftDeletes;
    protected $table = 'conf_add_ons';
    protected $fillable = ['admin_id', 'name', 'alias', 'description', 'basic_addons', 'module_id', 'status'];
    protected $dates = ['deleted_at'];
}
