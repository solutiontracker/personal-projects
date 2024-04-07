<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizerUserPermission extends Model
{
    use SoftDeletes;
    protected $table = 'conf_organizer_user_permissions';
    protected $fillable = ['id', 'organizer_user_id', 'permission_id', 'add_permissions', 'edit_permissions', 'delete_permissions', 'view_permissions', 'created_at', 'updated_at', 'deleted_at'];

    protected $dates = ['deleted_at'];

    public function permissions()
    {
        return $this->belongsTo('Models\Permission', 'permission_id', 'id');
    }
}
