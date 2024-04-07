<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventExhibitorAttendee extends Model
{
    protected $table = 'conf_event_exhibitor_attendees';
    protected $fillable = ['id', 'exhibitor_id', 'attendee_id', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function attendees()
    {
        return $this->belongsTo(Attendee::class, 'attendee_id', 'id');
    }
}
