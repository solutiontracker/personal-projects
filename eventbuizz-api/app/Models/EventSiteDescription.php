<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSiteDescription extends Model {

    use SoftDeletes;
    protected $table = 'conf_eventsite_description';
    protected $fillable = ['event_id'];
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\EventSiteDescriptionInfo', 'description_id');
    }
    
}
