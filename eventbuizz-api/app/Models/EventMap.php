<?php
namespace App\Models;

use App\Scopes\EventScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventMap extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_map';
    protected $fillable = ['event_id','status', 'google_map'];
    protected $dates = ['deleted_at'];
    public $timestamps = true;

    public function info()
    {
        return $this->hasMany('App\Models\MapInfo', 'map_id');
    }

}
