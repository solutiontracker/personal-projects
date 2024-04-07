<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizerPermission extends Model
{
    use SoftDeletes;
    protected $table = 'conf_organizer_permissions';
    protected $fillable = ['id', 'module_name', 'permissions_name', 'created_at', 'updated_at', 'deleted_at']; 
}
