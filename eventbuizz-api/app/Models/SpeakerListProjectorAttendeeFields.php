<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakerListProjectorAttendeeFields extends Model {
    use SoftDeletes;

    protected $table = 'conf_event_speaker_list_projector_attendee_fields';
    protected $fillable = ['event_id','fields_name','sort_order'];
    protected $dates = ['deleted_at'];

    public function event()
    {
        return $this->belongsTo('Events', 'event_id', 'id');
    }
}