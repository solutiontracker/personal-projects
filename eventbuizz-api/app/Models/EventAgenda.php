<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAgenda extends Model
{
    use SoftDeletes;
    
    protected $table = 'conf_event_agendas';
    protected $attributes = [
        'qa' => 0,
        'enable_checkin' => 0,
        'enable_speakerlist' => 0,
        'hide_on_registrationsite' => 0,
        'workshop_id' => 0
    ];
    protected $fillable = ['id', 'event_id', 'start_date', 'start_time', 'link_type', 'workshop_id', 'qa', 'ticket', 'enable_checkin', 'enable_speakerlist','hide_on_registrationsite','hide_time'];
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\AgendaInfo', 'agenda_id');
    }

    public function program_speakers()
    {
        return $this->belongsToMany('\App\Models\Attendee', 'conf_event_agenda_speakers', 'agenda_id', 'attendee_id')->whereNull('conf_event_agenda_speakers.deleted_at');
    }

    public function program_workshop()
    {
        return $this->belongsTo('\App\Models\EventWorkshop', 'workshop_id', 'id');
    }

    public function tracks()
    {
        return $this->belongsToMany('\App\Models\EventTrack', 'conf_event_agenda_tracks', 'agenda_id', 'track_id')->whereNull('conf_event_agenda_tracks.deleted_at');
    }

    public function video()
    {
        return $this->hasOne('\App\Models\AgendaVideo', 'agenda_id')->where('status', 1);
    }

    public function videos()
    {
        return $this->hasMany('\App\Models\AgendaVideo', 'agenda_id')->where('status', 1)->orderBy('sort')->orderBy('id');
    }

    public function groups()
    {
        return $this->belongsToMany('\App\Models\EventGroup', 'conf_event_agenda_groups', 'agenda_id', 'group_id')->whereNull('conf_event_agenda_groups.deleted_at');
    }

    /**
     * @api Registration site
     * @param $query
     * @param $event_id
     * @return mixed
     *
     */
    public function scopeOfEvent($query, $event_id)
    {
        return $query->where('event_id', $event_id);
    }

    public function program_attendees_attached()
    {
        return $this->hasMany('\App\Models\EventAgendaAttendeeAttached', 'agenda_id')->whereNull('conf_event_agenda_attendee_attached.deleted_at');
    }
}

