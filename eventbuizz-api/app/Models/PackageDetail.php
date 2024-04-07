<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageDetail extends Model
{

    use SoftDeletes;
    protected $table = 'conf_package_details';
    protected $fillable = ['admin_id', 'package_id', 'addons_id'];
    protected $dates = ['deleted_at'];
}
