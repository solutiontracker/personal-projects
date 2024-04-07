<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationLog extends Model
{
    protected $table = 'conf_reservation_logs';
    protected $fillable = ['slot_id', 'date', 'event_id', 'time_from', 'time_to', 'duration', 'entity_id', 'entity_type','master_id','contact_id','organizer_id','reserved_date'];

}