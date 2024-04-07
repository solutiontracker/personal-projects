<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    protected $table = 'conf_reservations';
    
    protected $fillable = ['date', 'event_id', 'time_from', 'time_to', 'duration', 'entity_id', 'entity_type','organizer_id'];
}
