<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubAdminLicence extends Model
{
    use SoftDeletes;
    protected $table = 'conf_sub_admin_licence';
    protected $fillable = ['name', 'type'];
    protected $dates = ['deleted_at'];
}
