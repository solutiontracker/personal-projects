<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignPackageUsed extends Model
{

    protected $table = 'conf_assign_package_used';
    protected $fillable = ['assign_package_id', 'event_id', 'is_expire', 'event_create_date', 'event_expire_date'];


    public function assignPackage()
    {
        return $this->belongsTo('AssignPackages', 'assign_package_id', 'id');
    }

	public function events()
	{
		return $this->belongsTo('Events', 'event_id', 'id');
	}


}
