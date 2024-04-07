<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventInternalBookingHotel extends Model
{
    use HasFactory , SoftDeletes;

    protected $table = 'conf_event_internal_booking_hotels';
    protected $fillable = ['id', 'event_id','name','rooms','price','sort_order','status','image','price_type','hotel_from_date','hotel_to_date', 'new_imp_flag'];
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('InternalBookingHotelsInfo', 'hotel_id');
    }
}
