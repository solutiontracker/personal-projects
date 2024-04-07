<?php
namespace App\Models;

use App\Scopes\EventScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class EventEventSitePhoto extends Model
{
    protected $table = 'conf_event_eventsite_photos';
    protected $fillable = ['id', 'image', 'event_id', 'status', 'sort_order', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;


    public function info()
    {
        return $this->hasMany(EventEventSitePhotoInfo::class, 'photo_id');
    }

}
