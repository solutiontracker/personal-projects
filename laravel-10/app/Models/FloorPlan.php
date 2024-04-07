<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FloorPlan extends Model
{
    use SoftDeletes;
    protected $table = 'conf_floor_plan';
    protected $fillable = ['event_id','organizer_id','document','image','status'];

}
