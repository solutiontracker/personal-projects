<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventWorkShopInfo extends Model {
    use SoftDeletes;
    protected $table = 'conf_event_workshops_info';
    protected $fillable = ['name','value','workshop_id','languages_id'];
    protected $dates = ['deleted_at'];
}