<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventHotel extends Model
{

	use SoftDeletes;
	protected $table = 'conf_event_hotels';
    protected $fillable = ['id', 'event_id','name','rooms','price','sort_order','status','is_archive','price_type','max_rooms','hotel_from_date','hotel_to_date', 'registration_form_id', 'url'];
	protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\EventHotelInfo', 'hotel_id');
    }

    public function room()
    {
        return $this->hasMany('App\Models\EventHotelRoom', 'hotel_id');
    }
}