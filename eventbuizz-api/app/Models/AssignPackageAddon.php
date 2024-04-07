<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignPackageAddon extends Model {

    protected $table = 'conf_assign_package_addons';
    protected $fillable = ['type', 'addon_id', 'assign_package_id'];

}
