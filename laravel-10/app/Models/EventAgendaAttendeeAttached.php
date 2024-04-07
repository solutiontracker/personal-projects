<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventAgendaAttendeeAttached extends Model
{
    use SoftDeletes;

    protected $table = 'conf_event_agenda_attendee_attached';

    protected $fillable = ['event_id', 'agenda_id', 'attendee_id', 'added_by', 'linked_from', 'link_id'];
    
    protected $dates = ['deleted_at'];

    public function agenda(){
        return $this->belongsTo(Agenda::class,'agenda_id','id');
    }
}