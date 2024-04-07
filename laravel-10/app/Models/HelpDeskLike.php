<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpDeskLike extends Model
{
    protected $table = 'conf_help_desk_likes';
    protected $fillable = ['id', 'event_id', 'attendee_id', 'help_desk_id', 'group_id'];

    use SoftDeletes;


    public function attendees()
    {
        return $this->belongsTo(Attendee::class, 'attendee_id');
    }
}
