<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAgendaTurnList extends Model
{
    protected $table = 'conf_event_agenda_turn_list';
    protected $fillable = ['event_id', 'status', 'sort_order', 'agenda_id', 'attendee_id', 'speech_start_time', 'moderator_speech_start_time', 'moderator_speech_end_time', 'notes'];

    use SoftDeletes;

    public function attendee()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id');
    }

}
