<?php
namespace App\Models;

use App\Scopes\EventScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class EventVideo extends Model
{
    protected $table = 'conf_event_videos';
    protected $fillable = ['id', 'thumnail', 'URL', 'type', 'event_id', 'status', 'video_path', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;

    public function info()
    {
        return $this->hasMany(EventVideoInfo::class, 'video_id');
    }
}
