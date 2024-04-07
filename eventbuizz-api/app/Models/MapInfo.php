<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MapInfo extends Model
{
    protected $table = 'conf_map_info';
    protected $fillable = ['name','value','map_id','languages_id','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
