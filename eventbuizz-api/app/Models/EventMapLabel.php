<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventMapLabel extends Model
{
    protected $table = 'conf_event_maps_labels';
    protected $fillable = ['event_id','label_id','language_id','value'];
}
