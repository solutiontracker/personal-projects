<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventEventSitePhotoInfo extends Model
{
    protected $table = 'conf_event_eventsite_photos_info';
    protected $fillable = ['id', 'name', 'value', 'photo_id', 'languages_id', 'status', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
