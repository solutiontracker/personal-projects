<?php
namespace App\Models;

use App\Scopes\EventScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAgendaSpeaker extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_agenda_speakers';
    protected $fillable = ['event_id','eventsite_show_home','agenda_id', 'attendee_id', 'sort_order','agenda_speaker_sort'];
    protected $dates = ['deleted_at'];

    public function attendee()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
    }

    public function program()
    {
        return $this->belongsTo('\App\Models\Agenda', 'agenda_id', 'id');
    }
    public function eventProgram()
    {
        return $this->belongsTo('\App\Models\EventAgenda', 'agenda_id', 'id');
    }

}