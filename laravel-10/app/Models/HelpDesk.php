<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpDesk extends Model
{
    protected $table = 'conf_help_desk';
    protected $fillable = ['answered','group_id', 'show_projector', 'q_startTime', 'isStart', 'displayed', 'sort_order', 'attendee_id', 'event_id', 'agenda_id', 'speaker_id', 'anonymous_user', 'like_count'];

    use SoftDeletes;

    public function info()
    {
        return $this->hasMany(HelpDeskInfo::class, 'help_desk_id');
    }

    public function group()
    {
        return $this->belongsTo(HelpDeskGroup::class, 'group_id');
    }
}
