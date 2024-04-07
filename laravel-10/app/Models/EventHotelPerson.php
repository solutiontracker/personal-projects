<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventHotelPerson extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_hotels_persons';

    protected $fillable = ['id', 'order_id', 'hotel_id', 'name', 'dob', 'attendee_id', 'order_hotel_id', 'room_no'];

    protected $dates = ['deleted_at'];

    public function attendee_detail()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
    }
}
