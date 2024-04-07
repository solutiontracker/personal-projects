<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventInternalBookingHotelAssigned extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'conf_event_internal_booking_hotel_assigned';
    protected $fillable = ['id', 'hotel_id','attendee_id','name','price','price_type','rooms','checkin','checkout'];
    protected $dates = ['deleted_at'];

    public function internal_booking_hotel(){
        return $this->belongsTo(EventInternalBookingHotel::class,'hotel_id', 'id');
    }
}
