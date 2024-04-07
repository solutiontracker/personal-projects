<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventsiteStreamingInfo extends Model {
    use SoftDeletes;
    protected $table = 'conf_eventsite_streaming_info';
    protected $fillable = ['name','value','event_id','languages_id','status'];
    protected $dates = ['deleted_at'];
}