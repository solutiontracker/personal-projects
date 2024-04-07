<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginHistory extends Model
{
    use SoftDeletes;

    protected $table = 'conf_login_history';
    protected $fillable = ['id', 'attendee_id', 'event_id', 'platform', 'ip', 'user_agent'];

    public function attendees()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
    }

    public function attendee()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
    }
    public function events()
    {
        return $this->belongsTo('\App\Models\Event', 'event_id', 'id');
    }
}
