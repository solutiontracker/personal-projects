<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventsiteStreaming extends Model
{
    use SoftDeletes;
    protected $table = 'conf_eventsite_streaming';
    protected $fillable = ['event_id'];
    protected $dates = ['deleted_at'];
}
