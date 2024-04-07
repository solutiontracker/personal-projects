<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubAdminLicenceAssign extends Model
{
    use SoftDeletes;
    protected $table = 'conf_sub_admin_licence_assign';
    protected $fillable = ['licence_id', 'organizer_id','status', 'licence_start_date', 'licence_end_date'];
    protected $dates = ['deleted_at'];

    public function licenceUsed()
    {
        return $this->hasMany('\App\Models\SubAdminLicenceAssignSubAdmin', 'assign_licence_id');
    }

    public function licences()
    {
        return $this->belongsTo('\App\Models\SubAdminLicence', 'licence_id', 'id');
    }

    public function organizer()
    {
        return $this->belongsTo('\App\Models\Organizer','organizer_id', 'id');
    }
}
