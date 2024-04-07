<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAgendaTrack extends Model
{

	use SoftDeletes;
	protected $table = 'conf_event_agenda_tracks';
    protected $fillable = ['track_id', 'agenda_id'];
	protected $dates = ['deleted_at'];

	public function tracks()
    {
        return $this->belongsTo('\App\Models\EventTrack','track_id');
    }

}