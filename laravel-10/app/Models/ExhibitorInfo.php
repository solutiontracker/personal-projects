<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExhibitorInfo extends Model
{
    protected $table = 'conf_exhibitors_info';
    protected $fillable = ['id', 'value', 'name', 'exhibitor_id', 'languages_id', 'status', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
