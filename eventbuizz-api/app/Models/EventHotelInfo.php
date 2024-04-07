<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventHotelInfo extends Model
{

	use SoftDeletes;
	protected $table = 'conf_event_hotels_info';
    protected $fillable = ['id', 'name','value','hotel_id','languages_id','status'];
	protected $dates = ['deleted_at'];
}