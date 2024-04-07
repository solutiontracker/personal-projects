<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventGalleryInfo extends Model
{
    protected $table = 'conf_event_gallery_info';
    protected $fillable = ['id', 'name', 'value', 'image_id', 'languages_id', 'status', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
