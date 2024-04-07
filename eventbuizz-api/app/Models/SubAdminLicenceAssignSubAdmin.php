<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubAdminLicenceAssignSubAdmin extends Model
{
    use SoftDeletes;
    protected $table = 'conf_sub_admin_licence_assign_sub_admin';
    protected $fillable = ['assign_licence_id', 'sub_admin_id', 'status', 'licence_start_date', 'licence_end_date'];
    protected $dates = ['deleted_at'];

    public function assignLicence()
    {
        return $this->belongsTo('\App\Models\SubAdminLicenceAssign', 'assign_licence_id', 'id');
    }
}
