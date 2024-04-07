<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventInternalBookingHotelRoomsAssigned extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'conf_event_internal_booking_hotel_rooms_assigned';
    protected $fillable = ['id', 'assign_hotel_id', 'room_id', 'hotel_id', 'event_id', 'reserve_date', 'rooms','attendee_id'];
    protected $dates = ['deleted_at'];
}
