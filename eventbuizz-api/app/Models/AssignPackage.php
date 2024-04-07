<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignPackage extends Model
{
    protected $table = 'conf_assign_packages';
    protected $fillable = ['admin_id', 'organizer_id', 'package_id', 'no_of_event', 'expire_duration', 'package_assign_date', 'package_expire_date'];

    public function assignPackageAddons()
    {
        return $this->belongsToMany('\App\Models\Addon', 'conf_assign_package_addons', 'assign_package_id', 'addons_id');
    }

    public function packageUsed()
    {
        return $this->hasMany('\App\Models\AssignPackageUsed', 'assign_package_id');
    }

}