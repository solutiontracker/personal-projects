<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttendeeType extends Model {

    protected $table = 'conf_event_attendee_type';

    protected $fillable = ['event_id', 'languages_id', 'sort_order', 'alias', 'attendee_type', 'is_basic', 'status'];

    public function event_attendee()
    {
        return $this->belongsTo('\App\Models\EventAttendee', 'attendee_type', 'id');
    }

    public function event_groups()
    {
        return $this->belongsToMany(EventGroup::class, 'conf_event_attendee_type_group', 'event_attendee_type_id', 'group_id')->whereNull('conf_event_attendee_type_group.deleted_at');
    }

}