<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventHotelRoom extends Model
{

	use SoftDeletes;
    protected $table = 'conf_event_hotels_rooms';
    protected $fillable = ['id', 'hotel_id', 'available_date', 'total_rooms'];
    protected $dates = ['deleted_at'];
}