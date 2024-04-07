<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;
    protected $table = 'conf_packages';
    protected $fillable = ['admin_id', 'name', 'description', 'no_of_event', 'expire_duration', 'status'];
    protected $dates = ['deleted_at'];
}
