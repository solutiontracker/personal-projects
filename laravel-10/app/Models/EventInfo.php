<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventInfo extends Model {

    protected $table = 'conf_event_info';
    protected $fillable = ['name','value','event_id','languages_id','status'];

}