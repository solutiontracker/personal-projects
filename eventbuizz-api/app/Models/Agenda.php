<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agenda extends Model
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
	protected $fillable = ['email', 'event_id', 'start_date', 'start_time', 'link_type', 'workshop_id', 'enable_speakerlist', 'hide_on_registrationsite', "enable_checkin", "ticket", "qa", 'show_program_on_check_in_app'];
	protected $dates = ['deleted_at'];

	public function info()
	{
		return $this->hasMany('\App\Models\AgendaInfo', 'agenda_id');
	}

	public function tracks()
	{
		return $this->belongsToMany('\App\Models\EventTrack', 'conf_event_agenda_tracks', 'agenda_id', 'track_id')->whereNull('conf_event_agenda_tracks.deleted_at');
	}

	public function groups()
	{
		return $this->belongsToMany('\App\Models\EventGroup', 'conf_event_agenda_groups', 'agenda_id', 'group_id')->whereNull('conf_event_agenda_groups.deleted_at');
	}

	public function attendee_assign()
	{
		return $this->hasMany('\App\Models\EventAgendaSpeaker', 'agenda_id');
	}

	public function video()
    {
        return $this->hasOne('\App\Models\AgendaVideo', 'agenda_id');
    }

    public function videos()
    {
        return $this->hasMany('\App\Models\AgendaVideo', 'agenda_id')->orderBy('sort')->orderBy('id');
    }
}
