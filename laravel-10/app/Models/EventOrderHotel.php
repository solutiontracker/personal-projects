<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventOrderHotel extends Model
{

    use SoftDeletes;

    protected $table = 'conf_event_order_hotels';

    protected $fillable = ['id', 'hotel_id', 'order_id', 'name', 'price', 'rooms', 'checkin', 'checkout', 'registration_form_id', 'attendee_id'];
    
    protected $dates = ['deleted_at'];

    public function persons()
    {
        return $this->hasMany('\App\Models\EventHotelPerson', 'order_id', 'order_id');
    }

    public function hotel_persons()
    {
        return $this->hasMany('\App\Models\EventHotelPerson', 'order_hotel_id')
            ->leftJoin('conf_attendees', 'conf_attendees.id', '=', 'conf_event_hotels_persons.attendee_id')
            ->select('conf_event_hotels_persons.*', 'conf_attendees.first_name', 'conf_event_hotels_persons.*', 'conf_attendees.last_name');
    }

    public function room()
    {
        return $this->hasMany('\App\Models\EventOrderHotelRoom', 'order_hotel_id');
    }
}
