<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTicketSetting extends Model
{
    protected $table = 'conf_event_tickets_settings';
    protected $fillable = ['event_id','show_price'];
    protected $dates = ['created_at','updated_at','deleted_at'];
}