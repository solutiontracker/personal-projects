<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddAttendeeLog extends Model
{
    use SoftDeletes;

    protected $table = 'conf_add_attendee_log';

    protected $fillable = ['id', 'attendee_id', 'event_id', 'organizer_id', 'type'];

    protected $dates = ['deleted_at'];


    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class, 'organizer_id');
    }

    public function attendee()
    {
        return $this->belongsTo(Attendee::class, 'attendee_id');
    }
}
