<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationSlotColleagueAttendee extends Model
{
    use SoftDeletes;
    protected $table = 'conf_reservation_slot_colleague_attendee';
    protected $fillable = ['event_id', 'entity_id', 'entity_type', 'slot_id', 'attendee_id'];
    protected $dates = ['deleted_at'];
}
