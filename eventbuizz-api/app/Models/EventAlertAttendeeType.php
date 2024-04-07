<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAlertAttendeeType extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'event_alert_attendee_types';

    protected $fillable = ['id', 'attendee_type_id', 'alert_id', 'status'];
}
