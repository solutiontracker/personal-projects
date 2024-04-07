<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingHotelSession extends Model
{

	use SoftDeletes;
	protected $table = 'conf_billing_hotels_sessions';
    protected $fillable = ['id','event_id', 'hotel_id','rooms','date_reserved','session_id','status'];
	protected $dates = ['deleted_at'];
}