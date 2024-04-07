<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeGroup extends Model {

    use SoftDeletes;

    protected $table = 'conf_event_attendees_groups';

    protected $fillable = ['group_id','attendee_id'];

    protected $dates = ['deleted_at'];

    public function group()
    {
        return $this->belongsTo('\App\Models\EventGroup', 'group_id', 'id');
    }

    public function attendee()
	{
		return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
	}
    
}