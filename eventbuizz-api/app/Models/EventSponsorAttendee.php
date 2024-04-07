<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSponsorAttendee extends Model
{
    protected $table = 'conf_event_sponsor_attendees';
    protected $fillable = ['id', 'sponsor_id', 'attendee_id', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function attendees()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
    }

    public function sponsor()
    {
        return $this->belongsTo('\App\Models\EventSponsor', 'sponsor_id', 'id');
    }

}
