<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventOrderHotelRoom extends Model
{

    use SoftDeletes;

    protected $table = 'conf_event_order_hotel_rooms';

    protected $fillable = ['id', 'order_hotel_id', 'room_id', 'hotel_id', 'order_id', 'event_id', 'reserve_date', 'rooms'];

	protected $dates = ['deleted_at'];

}
