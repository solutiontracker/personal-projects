<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventMobileApp extends Model {
    use SoftDeletes;
    protected $table = 'conf_event_mobileapp';
    protected $fillable = ['event_id','organizer_id','back_color','module','sizey','sizex','col','row','show_fav','with_image','latest_records','page','image','status'];
    protected $dates = ['deleted_at'];
}